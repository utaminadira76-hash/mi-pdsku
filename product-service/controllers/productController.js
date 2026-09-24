const productModel = require('../models/productModel');

// GET ambil semua products (yang sudah Anda buat)
async function index(req, res) {
    try {
        const products = await productModel.getAllProducts();
        res.json({
            message: "Berhasil mengambil data produk",
            data: products
        });
    } catch (error) {
        res.status(500).json({
            message: "Gagal mengambil data produk",
            error: error.message
        });
    }
}

// 1. GET - Get Product by ID
async function show(req, res) {
    try {
        const { id } = req.params;
        const product = await productModel.getProductById(id);
        
        if (!product) {
            return res.status(404).json({
                message: "Produk tidak ditemukan"
            });
        }

        res.json({
            message: "Berhasil mengambil detail produk",
            data: product
        });
    } catch (error) {
        res.status(500).json({
            message: "Gagal mengambil detail produk",
            error: error.message
        });
    }
}

// 2. POST - Create Product
async function store(req, res) {
    try {
        const newProductData = req.body;
        const createdProduct = await productModel.createProduct(newProductData);

        res.status(201).json({
            message: "Produk berhasil ditambahkan",
            data: createdProduct
        });
    } catch (error) {
        res.status(500).json({
            message: "Gagal menambahkan produk",
            error: error.message
        });
    }
}

// 3. PUT - Update Product
async function update(req, res) {
    try {
        const { id } = req.params;
        const updateData = req.body;
        
        const updatedProduct = await productModel.updateProduct(id, updateData);

        if (!updatedProduct) {
            return res.status(404).json({
                message: "Produk yang akan di-update tidak ditemukan"
            });
        }

        res.json({
            message: "Produk berhasil diperbarui",
            data: updatedProduct
        });
    } catch (error) {
        res.status(500).json({
            message: "Gagal memperbarui produk",
            error: error.message
        });
    }
}

// 4. DELETE - Delete Product
async function destroy(req, res) {
    try {
        const { id } = req.params;
        const deletedProduct = await productModel.deleteProduct(id);

        if (!deletedProduct) {
            return res.status(404).json({
                message: "Produk yang akan dihapus tidak ditemukan"
            });
        }

        res.json({
            message: "Produk berhasil dihapus"
        });
    } catch (error) {
        res.status(500).json({
            message: "Gagal menghapus produk",
            error: error.message
        });
    }
}

// Jangan lupa export semua fungsi agar bisa dipakai di routes
module.exports = {
    index,
    show,
    store,
    update,
    destroy
};