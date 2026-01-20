<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderShippingUpdate;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;
use App\Models\ProductStockAudit;
use App\Models\ProductVariant;
use App\Mail\AdminCustomerEmailMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    private function applyStockUpdate(Product $product, bool $unlimited, ?int $quantity, ?string $note = null): void
    {
        $beforeQty = (int) ($product->stock ?? 0);
        $beforeUnlimited = (bool) ($product->stock_unlimited ?? false);

        $nextUnlimited = $unlimited;
        $nextQty = $beforeQty;
        if (!$nextUnlimited) {
            $nextQty = max(0, (int) ($quantity ?? 0));
        }

        if ($beforeUnlimited === $nextUnlimited && $beforeQty === $nextQty) {
            return;
        }

        $product->stock_unlimited = $nextUnlimited;
        if (!$nextUnlimited) {
            $product->stock = $nextQty;
        }
        $product->stock_updated_at = now();
        $product->save();

        ProductStockAudit::create([
            'product_id' => $product->product_id,
            'admin_user_id' => auth('admin')->id() ?? auth()->id(),
            'before_quantity' => $beforeQty,
            'after_quantity' => (int) ($product->stock ?? 0),
            'before_unlimited' => $beforeUnlimited,
            'after_unlimited' => (bool) ($product->stock_unlimited ?? false),
            'note' => $note,
        ]);
    }

    /**
     * Display the admin dashboard with key statistics
     */
    public function dashboard()
    {
        $revenueQuery = Order::query()->where('status', '!=', 'cancelled');

        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            // Include archived (soft-deleted) messages in total.
            'total_contacts' => Contact::withTrashed()->count(),
            // Revenue based on placed orders (excludes cancelled)
            'total_revenue' => (float) $revenueQuery->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders' => Order::where('status', 'shipped')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            // Contacts table uses `status` enum: new/read/responded
            'unread_contacts' => Contact::where('status', 'new')->count(),
        ];

        $recent_orders = Order::with('user')
            ->latest('created_at')
            ->take(5)
            ->get();

        $recent_contacts = Contact::latest('created_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_orders', 'recent_contacts'));
    }

    /**
     * Show list of all products
     */
    public function indexProducts(Request $request)
    {
        $selectedCategory = (string) $request->query('category', '');
        $onlyFeatured = (bool) $request->boolean('featured');
        $search = trim((string) $request->query('q', ''));
        $searchEscaped = addcslashes($search, "\\%_");
        $searchId = ctype_digit($search) ? (int) $search : null;
        $categories = Category::query()->orderBy('name')->pluck('name')->values();

        $query = Product::query()
            ->withCount(['variants as variants_count' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['variants as variants_unlimited_count' => function ($q) {
                $q->where('is_active', true)->where('stock_unlimited', true);
            }])
            ->withSum(['variants as variants_stock_sum' => function ($q) {
                $q->where('is_active', true);
            }], 'stock')
            ->when($selectedCategory !== '', function ($q) use ($selectedCategory) {
                return $q->where('category', $selectedCategory);
            })
            ->when($onlyFeatured, function ($q) {
                return $q->where('is_featured', true);
            })
            ->when($search !== '', function ($q) use ($searchEscaped, $searchId) {
                return $q->where(function ($sub) use ($searchEscaped, $searchId) {
                    $like = '%' . $searchEscaped . '%';
                    $sub->where('product_name', 'like', $like)
                        ->orWhere('description', 'like', $like);

                    if ($searchId !== null) {
                        $sub->orWhere('product_id', $searchId);
                    }
                });
            })
            ->orderBy('product_id', 'asc');

        $products = $query->paginate(15)->appends($request->query());

        return view('admin.products.index', compact('products', 'categories', 'selectedCategory', 'onlyFeatured'));
    }

    /**
     * Show form to create new product
     */
    public function createProduct()
    {
        $categories = Category::pluck('name')->toArray();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product
     */
    public function storeProduct(Request $request)
    {
        $categoryNames = Category::pluck('name')->toArray();

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:' . implode(',', $categoryNames),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'sometimes|boolean',
            'stock_unlimited' => 'sometimes|boolean',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $validated['image_path'] = 'images/' . $imageName;
        }
        unset($validated['image']);

        $unlimited = $request->boolean('stock_unlimited');
        $validated['stock_unlimited'] = $unlimited;
        $validated['stock'] = $unlimited ? (int) ($validated['stock'] ?? 0) : max(0, (int) ($validated['stock'] ?? 0));
        $validated['stock_updated_at'] = now();
        $validated['is_featured'] = $request->boolean('is_featured');

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show form to edit a product
     */
    public function editProduct(Product $product)
    {
        $categories = Category::pluck('name')->toArray();
        $product->load([
            'variants' => function ($q) {
                $q->orderBy('name');
            },
            'stockAudits' => function ($q) {
            $q->with('adminUser')->latest()->take(20);
            },
        ]);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product
     */
    public function updateProduct(Request $request, Product $product)
    {
        $categoryNames = Category::pluck('name')->toArray();

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|in:' . implode(',', $categoryNames),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'sometimes|boolean',
            'stock_unlimited' => 'sometimes|boolean',
            'stock' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images'), $imageName);
            $validated['image_path'] = 'images/' . $imageName;
        }
        unset($validated['image']);

        $unlimited = $request->boolean('stock_unlimited');
        $validated['is_featured'] = $request->boolean('is_featured');
        $stockQty = array_key_exists('stock', $validated) ? $validated['stock'] : null;
        unset($validated['stock_unlimited'], $validated['stock']);

        $variantsExisting = $request->input('variants', []);
        $variantsNew = $request->input('variants_new', []);

        // Best-effort guard against duplicate variant names (unique per product)
        $names = [];
        foreach (is_array($variantsExisting) ? $variantsExisting : [] as $row) {
            if (!is_array($row) || !empty($row['_delete'])) continue;
            $name = trim((string) ($row['name'] ?? ''));
            if ($name !== '') $names[] = mb_strtolower($name);
        }
        foreach (is_array($variantsNew) ? $variantsNew : [] as $row) {
            if (!is_array($row)) continue;
            $name = trim((string) ($row['name'] ?? ''));
            if ($name !== '') $names[] = mb_strtolower($name);
        }
        $dupes = collect($names)->countBy()->filter(fn ($c) => $c > 1);
        if ($dupes->isNotEmpty()) {
            return redirect()->back()->withInput()->with('error', 'Variant names must be unique per product.');
        }

        DB::transaction(function () use ($product, $validated, $unlimited, $stockQty, $variantsExisting, $variantsNew, $request) {
            $locked = Product::where('product_id', $product->product_id)->lockForUpdate()->firstOrFail();
            $locked->update($validated);
            $this->applyStockUpdate($locked, $unlimited, $stockQty, 'Updated via product edit form');

            if ($request->has('variants') || $request->has('variants_new')) {
                // Update existing variants
                foreach (is_array($variantsExisting) ? $variantsExisting : [] as $variantId => $row) {
                    $variantIdInt = (int) $variantId;
                    if ($variantIdInt <= 0 || !is_array($row)) {
                        continue;
                    }

                    $variant = ProductVariant::where('id', $variantIdInt)
                        ->where('product_id', $locked->product_id)
                        ->lockForUpdate()
                        ->first();
                    if (!$variant) {
                        continue;
                    }

                    $delete = !empty($row['_delete']);
                    if ($delete) {
                        $variant->is_active = false;
                        $variant->save();
                        continue;
                    }

                    $name = trim((string) ($row['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }

                    $priceRaw = $row['price'] ?? null;
                    $price = ($priceRaw === null || $priceRaw === '') ? null : (float) $priceRaw;

                    $stockUnlimited = !empty($row['stock_unlimited']);
                    $stock = $stockUnlimited ? (int) ($variant->stock ?? 0) : max(0, (int) ($row['stock'] ?? 0));

                    $variant->name = $name;
                    $variant->price = $price;
                    $variant->stock_unlimited = $stockUnlimited;
                    $variant->stock = $stock;
                    $variant->is_active = (bool) ($row['is_active'] ?? false);
                    $variant->save();
                }

                // Create new variants
                foreach (is_array($variantsNew) ? $variantsNew : [] as $row) {
                    if (!is_array($row)) {
                        continue;
                    }
                    $name = trim((string) ($row['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }

                    $priceRaw = $row['price'] ?? null;
                    $price = ($priceRaw === null || $priceRaw === '') ? null : (float) $priceRaw;

                    $stockUnlimited = !empty($row['stock_unlimited']);
                    $stock = $stockUnlimited ? 0 : max(0, (int) ($row['stock'] ?? 0));

                    ProductVariant::create([
                        'product_id' => $locked->product_id,
                        'name' => $name,
                        'price' => $price,
                        'stock_unlimited' => $stockUnlimited,
                        'stock' => $stock,
                        'is_active' => (bool) ($row['is_active'] ?? false),
                    ]);
                }
            }
        });

        $redirectTo = $request->input('redirect_to');
        if (is_string($redirectTo) && $redirectTo !== '') {
            $parsed = @parse_url($redirectTo);
            $host = is_array($parsed) ? ($parsed['host'] ?? null) : null;
            $path = is_array($parsed) ? ($parsed['path'] ?? null) : null;
            $query = is_array($parsed) ? ($parsed['query'] ?? null) : null;

            // Allow same-host absolute URLs or local relative URLs only.
            $appHost = parse_url(url('/'), PHP_URL_HOST);
            if ($host === null && is_string($redirectTo) && str_starts_with($redirectTo, '/')) {
                return redirect($redirectTo)->with('success', 'Product updated successfully!');
            }
            if ($host !== null && $appHost !== null && $host === $appHost && is_string($path)) {
                $dest = $path . ($query ? ('?' . $query) : '');
                return redirect($dest)->with('success', 'Product updated successfully!');
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * JSON endpoint: update stock quantity / unlimited toggle
     */
    public function updateProductStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'unlimited' => 'required|boolean',
            'quantity' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:255',
        ]);

        if (!$validated['unlimited'] && $validated['quantity'] === null) {
            return response()->json([
                'success' => false,
                'message' => 'Quantity is required when stock is not unlimited.',
            ], 422);
        }

        DB::transaction(function () use ($product, $validated) {
            $locked = Product::where('product_id', $product->product_id)->lockForUpdate()->firstOrFail();
            $this->applyStockUpdate(
                $locked,
                (bool) $validated['unlimited'],
                $validated['quantity'] !== null ? (int) $validated['quantity'] : null,
                $validated['note'] ?? null
            );
        });

        $product->refresh();

        return response()->json([
            'success' => true,
            'product_id' => $product->product_id,
            'unlimited' => (bool) ($product->stock_unlimited ?? false),
            'quantity' => (int) ($product->stock ?? 0),
            'updated_at' => optional($product->stock_updated_at)->toIso8601String(),
        ]);
    }

    /**
     * Delete the specified product
     */
    public function destroyProduct(Product $product)
    {
        $product->delete();

        $redirectTo = request()->input('redirect_to');
        if (is_string($redirectTo) && $redirectTo !== '') {
            $parsed = @parse_url($redirectTo);
            $host = is_array($parsed) ? ($parsed['host'] ?? null) : null;
            $path = is_array($parsed) ? ($parsed['path'] ?? null) : null;
            $query = is_array($parsed) ? ($parsed['query'] ?? null) : null;
            $appHost = parse_url(url('/'), PHP_URL_HOST);

            if ($host === null && is_string($redirectTo) && str_starts_with($redirectTo, '/')) {
                return redirect($redirectTo)->with('success', 'Product deleted successfully!');
            }
            if ($host !== null && $appHost !== null && $host === $appHost && is_string($path)) {
                $dest = $path . ($query ? ('?' . $query) : '');
                return redirect($dest)->with('success', 'Product deleted successfully!');
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show list of all orders
     */
    public function indexOrders(Request $request)
    {
        $status = (string) $request->query('status', '');
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'];

        $ordersQuery = Order::with('user')->latest('created_at');
        if ($status !== '' && in_array($status, $allowedStatuses, true)) {
            $ordersQuery->where('status', $status);
        }

        $orders = $ordersQuery->paginate(15)->appends($request->query());
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        $order->load('user', 'items.product', 'shippingUpdates');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,completed,cancelled',
        ]);

        $next = (string) $validated['status'];
        if ($next === 'delivered' && !$order->delivered_at) {
            $validated['delivered_at'] = now();
        }
        if ($next === 'completed' && !$order->completed_at) {
            $validated['completed_at'] = now();
        }

        $order->update($validated);

        // Best-effort: create a matching shipping update for major status changes.
        if (in_array($next, ['processing', 'shipped', 'delivered'], true)) {
            OrderShippingUpdate::create([
                'order_id' => $order->id,
                'admin_user_id' => auth('admin')->id() ?? auth()->id(),
                'status' => $next,
                'location' => null,
                'message' => $next === 'processing'
                    ? 'Order is being processed'
                    : ($next === 'shipped' ? 'Order has shipped' : 'Order delivered'),
                'occurred_at' => now(),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Order status updated successfully!');
    }

    public function storeOrderShippingUpdate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|max:40',
            'message' => 'required|string|max:255',
            'location' => 'nullable|string|max:120',
            'occurred_at' => 'nullable|date',
        ]);

        $occurredAt = !empty($validated['occurred_at']) ? $validated['occurred_at'] : now();

        OrderShippingUpdate::create([
            'order_id' => $order->id,
            'admin_user_id' => auth('admin')->id() ?? auth()->id(),
            'status' => (string) $validated['status'],
            'location' => $validated['location'] ?? null,
            'message' => (string) $validated['message'],
            'occurred_at' => $occurredAt,
        ]);

        // If admin posts a delivered update, set order delivered.
        if ((string) $validated['status'] === 'delivered') {
            if ((string) $order->status !== 'completed') {
                $order->status = 'delivered';
            }
            if (!$order->delivered_at) {
                $order->delivered_at = now();
            }
            $order->save();
        }

        return redirect()->back()->with('success', 'Shipping update added.');
    }

    public function indexReturns(Request $request)
    {
        $status = (string) $request->query('status', '');
        $query = OrderReturn::with(['order.user'])
            ->latest('created_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        $returns = $query->paginate(15)->appends($request->query());
        return view('admin.returns.index', compact('returns', 'status'));
    }

    public function showReturn(OrderReturn $orderReturn)
    {
        $orderReturn->load(['order.user', 'items.orderItem.product']);
        return view('admin.returns.show', compact('orderReturn'));
    }

    public function updateReturnStatus(Request $request, OrderReturn $orderReturn)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:requested,approved,rejected,in_transit,received,closed',
        ]);

        $next = (string) $validated['status'];
        $patch = ['status' => $next];
        if ($next === 'approved' && !$orderReturn->approved_at) {
            $patch['approved_at'] = now();
        }
        if ($next === 'rejected' && !$orderReturn->rejected_at) {
            $patch['rejected_at'] = now();
            $patch['closed_at'] = $orderReturn->closed_at ?? now();
        }
        if ($next === 'closed' && !$orderReturn->closed_at) {
            $patch['closed_at'] = now();
        }

        $orderReturn->update($patch);

        return redirect()->back()->with('success', 'Return status updated.');
    }

    /**
     * Show list of all contacts
     */
    public function indexContacts(Request $request)
    {
        $unread = (bool) $request->boolean('unread');

        $contactsQuery = Contact::latest('created_at');
        if ($unread) {
            $contactsQuery->where('status', 'new');
        }

        $contacts = $contactsQuery->paginate(15)->appends($request->query());
        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Show archived (soft-deleted) contacts ("Recently Deleted").
     */
    public function indexArchivedContacts(Request $request)
    {
        $contacts = Contact::onlyTrashed()
            ->latest('deleted_at')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.contacts.archived', compact('contacts'));
    }

    /**
     * Show contact message details
     */
    public function showContact(Contact $contact)
    {
        if (($contact->status ?? 'new') === 'new') {
            $contact->update(['status' => 'read']);
        }
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Email a customer directly from a contact message.
     */
    public function emailContact(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:5000',
        ]);

        Mail::to($contact->email)->send(new AdminCustomerEmailMail(
            customerName: (string) $contact->name,
            emailSubject: (string) $validated['subject'],
            messageBody: (string) $validated['message'],
            originalMessage: (string) ($contact->message ?? ''),
        ));

        // Mark message as responded after emailing
        if (($contact->status ?? 'new') !== 'responded') {
            $contact->update(['status' => 'responded']);
        }

        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'Email sent successfully!');
    }

    /**
     * Delete contact message
     */
    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Message archived. You can restore it from Archived.');
    }

    /**
     * Restore an archived contact message.
     */
    public function restoreContact(int $contact)
    {
        $message = Contact::onlyTrashed()->findOrFail($contact);
        $message->restore();

        return redirect()->route('admin.contacts.archived')
            ->with('success', 'Message restored successfully!');
    }

    /**
     * Permanently delete an archived contact message.
     */
    public function forceDestroyContact(int $contact)
    {
        $message = Contact::onlyTrashed()->findOrFail($contact);
        $message->forceDelete();

        return redirect()->route('admin.contacts.archived')
            ->with('success', 'Message permanently deleted.');
    }

    /**
     * Show list of all users
     */
    public function indexUsers()
    {
        $users = User::where('role', '!=', 'admin')
            ->orderByRaw('banned_at is null desc')
            ->orderByDesc('created_at')
            ->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user_orders = $user->orders()->latest()->get();
        return view('admin.users.show', compact('user', 'user_orders'));
    }

    /**
     * Ban (archive) a user account.
     * This does NOT permanently delete the account.
     */
    public function destroyUser(User $user)
    {
        // Prevent banning admin accounts and/or the currently logged-in admin user.
        if (($user->role ?? 'customer') === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot ban an admin account.');
        }

        if (auth('admin')->check() && auth('admin')->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot ban the account you are currently using.');
        }

        DB::transaction(function () use ($user) {
            // Clean up related auth/session data (force logout)
            DB::table('sessions')->where('user_id', $user->id)->delete();
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            $user->banned_at = now();
            if (empty($user->banned_reason)) {
                $user->banned_reason = 'Temporarily disabled by admin';
            }
            $user->save();
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'User banned (archived) successfully!');
    }

    /**
     * Restore (unban) a previously banned user.
     */
    public function restoreUser(User $user)
    {
        if (($user->role ?? 'customer') === 'admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot restore an admin account.');
        }

        $user->banned_at = null;
        $user->banned_reason = null;
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'User restored successfully!');
    }

    /**
     * Show list of categories
     */
    public function indexCategories()
    {
        // Products store category as a free-form string; normalize to avoid case/whitespace mismatches.
        $countsByKey = Product::query()
            ->whereNotNull('category')
            ->selectRaw('LOWER(TRIM(category)) as category_key, COUNT(*) as aggregate')
            ->groupBy('category_key')
            ->pluck('aggregate', 'category_key');

        $categories = Category::query()->get()->map(function ($cat) use ($countsByKey) {
            $key = Str::of($cat->name)->trim()->lower()->toString();
            $cat->products_count = (int) ($countsByKey[$key] ?? 0);
            return $cat;
        });

        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $validated['category_name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.categories.index');
    }

    public function editCategory($category)
    {
        $category = Category::findOrFail($category);
        return view('admin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, $category)
    {
        $category = Category::findOrFail($category);

        $oldName = (string) ($category->name ?? '');

        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $newName = (string) $validated['category_name'];

        $category->update([
            'name' => $newName,
            'description' => $validated['description'] ?? null,
        ]);

        // If the category name changes, update existing products that reference it.
        // Products store category as a string, so this keeps admin counts and storefront filters consistent.
        $oldKey = Str::of($oldName)->trim()->lower()->toString();
        $newKey = Str::of($newName)->trim()->lower()->toString();
        if ($oldKey !== '' && $newKey !== '' && $oldKey !== $newKey) {
            Product::query()
                ->whereNotNull('category')
                ->whereRaw('LOWER(TRIM(category)) = ?', [$oldKey])
                ->update(['category' => $newName]);
        }

        return redirect()->route('admin.categories.index');
    }

    public function destroyCategory($category)
    {
        $category = Category::findOrFail($category);
        $category->delete();
        return redirect()->route('admin.categories.index');
    }
}
