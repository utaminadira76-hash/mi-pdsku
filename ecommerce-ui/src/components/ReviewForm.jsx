import { useState } from 'react'
import { createReview } from '../api/reviewApi'
import { describeError } from '../utils/format'

export default function ReviewForm({ productId, userId, onCreated }) {
  const [rating, setRating] = useState(5)
  const [comment, setComment] = useState('')
  const [message, setMessage] = useState(null)
  const [submitting, setSubmitting] = useState(false)

  async function handleSubmit(e) {
    e.preventDefault()
    setMessage(null)
    setSubmitting(true)
    try {
      await createReview({
        productId,
        userId,
        rating: Number(rating),
        comment: comment.trim(),
      })
      setComment('')
      setRating(5)
      setMessage({ type: 'success', text: 'Review terkirim.' })
      if (onCreated) onCreated()
    } catch (err) {
      setMessage({ type: 'error', text: describeError(err) })
    } finally {
      setSubmitting(false)
    }
  }

  return (
    <form className="form" onSubmit={handleSubmit}>
      <h2>Tulis Review</h2>
      <label>
        Rating
        <select
          value={rating}
          onChange={(e) => setRating(e.target.value)}
          aria-label="Rating"
        >
          {[5, 4, 3, 2, 1].map((n) => (
            <option key={n} value={n}>
              {n} bintang
            </option>
          ))}
        </select>
      </label>
      <label>
        Komentar
        <textarea
          rows={3}
          value={comment}
          onChange={(e) => setComment(e.target.value)}
          placeholder="Tulis komentar (opsional)"
        />
      </label>
      <button type="submit" disabled={submitting}>
        {submitting ? 'Mengirim...' : 'Kirim Review'}
      </button>
      {message && (
        <p className={`alert alert-${message.type}`}>{message.text}</p>
      )}
    </form>
  )
}