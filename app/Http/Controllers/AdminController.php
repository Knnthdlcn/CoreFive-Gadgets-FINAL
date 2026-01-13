<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display the admin dashboard with key statistics
     */
    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_users' => User::count(),
            'total_contacts' => Contact::count(),
            'total_revenue' => Order::sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
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
    public function indexProducts()
    {
        $products = Product::paginate(15);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show form to create new product
     */
    public function createProduct()
    {
        return view('admin.products.create');
    }

    /**
     * Store a newly created product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:Phones,Computing,Accessories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['name'] = $validated['product_name'];
        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show form to edit a product
     */
    public function editProduct(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified product
     */
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:Phones,Computing,Accessories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $validated['image_path'] = $imagePath;
        }

        $validated['name'] = $validated['product_name'];
        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Delete the specified product
     */
    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show list of all orders
     */
    public function indexOrders()
    {
        $orders = Order::with('user')->latest('created_at')->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show order details
     */
    public function showOrder(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->back()
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Show list of all contacts
     */
    public function indexContacts()
    {
        $contacts = Contact::latest('created_at')->paginate(15);
        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Show contact message details
     */
    public function showContact(Contact $contact)
    {
        $contact->update(['read_at' => now()]);
        return view('admin.contacts.show', compact('contact'));
    }

    /**
     * Delete contact message
     */
    public function destroyContact(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')
            ->with('success', 'Contact message deleted successfully!');
    }

    /**
     * Show list of all users
     */
    public function indexUsers()
    {
        $users = User::latest('created_at')->paginate(15);
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
     * Show list of categories
     */
    public function indexCategories()
    {
        $categories = ['Phones', 'Computing', 'Accessories'];
        $product_counts = [];
        foreach ($categories as $cat) {
            $product_counts[$cat] = Product::where('category', $cat)->count();
        }
        return view('admin.categories.index', compact('categories', 'product_counts'));
    }
}
