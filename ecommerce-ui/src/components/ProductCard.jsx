import { Link } from 'react-router-dom'
import StarRating from './StarRating'
import { formatRupiah } from '../utils/format'

export default function ProductCard({ product }) {
  return (
    <article className="card">
      <h2>
        <Link to={`/products/${product.id}`}>{product.name}</Link>
      </h2>
      <p className="price">{formatRupiah(product.price)}</p>
      {product.rating ? (
        <StarRating
          average={product.rating.average_rating}
          total={product.rating.total_reviews}
        />
      ) : (
        <span className="muted">Rating tidak tersedia</span>
      )}
    </article>
  )
}