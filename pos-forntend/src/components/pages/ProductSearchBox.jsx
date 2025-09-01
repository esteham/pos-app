/* eslint-disable no-unused-vars */
import React, { useEffect, useRef, useState } from 'react'
import { searchProducts } from '../api/client.js'

export default function ProductSearchBox({ onSelect }) {
	const [q, setQ] = useState('')
	const [list, setList] = useState([])
	const [open, setOpen] = useState(false)
	const timer = useRef()

	useEffect(() => {
		if (timer.current) clearTimeout(timer.current)
		if (!q) {
			setList([])
			setOpen(false)
			return
		}

		timer.current = setTimeout(async () => {
			try {
				const { data } = await searchProducts(q)
				setList(data || [])
				setOpen(true)
			} catch (e) {
				// optionally handle error
				setList([])
				setOpen(false)
			}
		}, 250)

		return () => {
			if (timer.current) clearTimeout(timer.current)
		}
	}, [q])

	const pick = (p) => {
		onSelect && onSelect(p)
		setQ('')
		setList([])
		setOpen(false)
	}

	return (
		<div className="position-relative">
			<label>Search By Name / SKU / barcode / ID</label>
			<input
				className="form-control"
				placeholder="Type to search..."
				value={q}
				onChange={(e) => setQ(e.target.value)}
				onFocus={() => list.length && setOpen(true)}
				onBlur={() => setTimeout(() => setOpen(false), 150)} // short delay to allow click
			/>

			{open && (
				<div
					className="list-group position-absolute w-100"
					style={{ zIndex: 1000, maxHeight: 300, overflowY: 'auto' }}
				>
					{list.length > 0 ? (
						list.map((p) => (
							<button
								key={p.id}
								type="button"
								className="list-group-item list-group-item-action"
								onMouseDown={() => pick(p)} // onMouseDown preserves click before blur
							>
								<div className="d-flex justify-content-between">
									<strong>{p.name}</strong>
									<small>${p.price} | {p.unit} | Stock {p.stock}</small>
								</div>
								<small className="text-muted">
									SKU: {p.sku} {p.barcode ? ` | Barcode: ${p.barcode}` : ''}
								</small>
							</button>
						))
					) : (
						<div className="list-group-item">No result</div>
					)}
				</div>
			)}
		</div>
	)
}
