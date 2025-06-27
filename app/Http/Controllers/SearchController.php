<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    // product search
    public function searchProducts(Request $request)
    {
        $search = $request->search;
        $products = Product::where('productName', 'like', '%' . $search . '%')
            ->orWhere('productCategory', 'like', '%' . $search . '%')
            ->orWhere('productBrand', 'like', '%' . $search . '%')
            ->paginate(4);
        return view('home.search', compact('products'));
    }
    public function showProducts()
    {
        $products = Product::paginate(8);
        return view('home.search', compact('products'));
    }

    public function showConsoleProducts()
    {
        $products = Product::where('productCategory', 'console')->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showControllerProducts()
    {
        $products = Product::where('productCategory', 'controller')->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showDiscProducts()
    {
        $products = Product::where('productCategory', 'disc')->paginate(8);
        return view('home.search', compact('products'));
    }

    // sony search
    public function showSonyProducts()
    {
        $products = Product::where('productBrand', 'sony')->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showConsoleSonyProducts()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'sony')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    public function showControllerSonyProducts()
    {
        $products = Product::where('productCategory', 'controller')
            ->where('productBrand', 'sony')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showDiscSonyProducts()
    {
        $products = Product::where('productCategory', 'disc')
            ->where('productBrand', 'sony')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    public function showPS5()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'sony')
            ->where('productName', 'like', '%PS5%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showPS4()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'sony')
            ->where('productName', 'like', '%PS4%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showPSV()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'sony')
            ->where('productName', 'like', '%PS Vita%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showPSP()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'sony')
            ->where('productName', 'like', '%PSP%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    // nintendo search
    public function showNintendoProducts()
    {
        $products = Product::where('productBrand', 'nintendo')->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showConsoleNintendoProducts()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'nintendo')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    public function showControllerNintendoProducts()
    {
        $products = Product::where('productCategory', 'controller')
            ->where('productBrand', 'nintendo')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showDiscNintendoProducts()
    {
        $products = Product::where('productCategory', 'disc')
            ->where('productBrand', 'nintendo')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    public function showSwitch()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'nintendo')
            ->where('productName', 'like', '%Nintendo Switch%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function show3DS()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'nintendo')
            ->where('productName', 'like', '%Nintendo 3DS%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    // xbox search
    public function showXboxProducts()
    {
        $products = Product::where('productBrand', 'xbox')->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showConsoleXboxProducts()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'xbox')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showControllerXboxProducts()
    {
        $products = Product::where('productCategory', 'controller')
            ->where('productBrand', 'xbox')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showDiscXboxProducts()
    {
        $products = Product::where('productCategory', 'disc')
            ->where('productBrand', 'xbox')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    public function showXboxSeries()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'xbox')
            ->where('productName', 'like', '%Xbox Series%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showXboxOne()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'xbox')
            ->where('productName', 'like', '%Xbox One%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showXbox360()
    {
        $products = Product::where('productCategory', 'console')
            ->where('productBrand', 'xbox')
            ->where('productName', 'like', '%Xbox 360%')
            ->paginate(8);
        return view('home.search', compact('products'));
    }

    // accessory search
    public function showAccessory()
    {
        $products = Product::where('productCategory', 'accessory')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showAccessorySony()
    {
        $products = Product::where('productCategory', 'accessory')
            ->where('productBrand', 'sony')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showAccessoryNintendo()
    {
        $products = Product::where('productCategory', 'accessory')
            ->where('productBrand', 'nintendo')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
    public function showAccessoryXbox()
    {
        $products = Product::where('productCategory', 'accessory')
            ->where('productBrand', 'xbox')
            ->paginate(8);
        return view('home.search', compact('products'));
    }
}
