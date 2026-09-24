const express = require('express');
const router = express.Router();
const productController = require('../controllers/productController');

// Daftar Endpoint CRUD Product Service
router.get('/', productController.index);          // GET - Mengambil semua produk
router.get('/:id', productController.show);       // GET - Mengambil produk berdasarkan ID
router.post('/', productController.store);        // POST - Membuat produk baru
router.put('/:id', productController.update);     // PUT - Memperbarui produk berdasarkan ID
router.delete('/:id', productController.destroy); // DELETE - Menghapus produk berdasarkan ID

module.exports = router;