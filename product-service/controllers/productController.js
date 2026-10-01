const productModel = require('../models/productModel');

// Fungsi helper/pendukung untuk memvalidasi image Base64
function validateImage(image) {
    // 1. Cek jika field image kosong atau tidak dikirim
    if (!image) {
        return "Field image wajib diisi";
    }

    // 2. Cek format Base64 yang valid (Regex & panjang habis dibagi 4)
    const base64Regex = /^[A-Za-z0-9+/]+={0,2}$/;
    if (!base64Regex.test(image) || image.length % 4 !== 0) {
        return "Field image harus berupa Base64 yang valid";
    }

    // 3. Cek ukuran hasil decode (maksimal 2 MB = 2 * 1024 * 1024 byte)
    const imageBuffer = Buffer.from(image, 'base64');
    const maxSize = 2 * 1024 * 1024; 
    if (imageBuffer.length > maxSize) {
        return "Ukuran image maksimal 2 MB";
    }

    return null; // Berarti valid (tidak ada error)
}

// GET ambil semua products
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
        const { image } = req.body;

        // Jalankan validasi image
        const validationError = validateImage(image);
        if (validationError) {
            return res.status(400).json({ message: validationError });
        }

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
        const { image } = req.body;

        // Jalankan validasi image (jika di-update, pastikan image-nya juga valid)
        const validationError = validateImage(image);
        if (validationError) {
            return res.status(400).json({ message: validationError });
        }

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

module.exports = {
    index,
    show,
    store,
    update,
    destroy
};