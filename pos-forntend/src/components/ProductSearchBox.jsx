import React, { useEffect, useRef, useState } from 'react'
import { searchProducts } from '../api/client.js'

export default function ProductSearchBox({onSelect, placeholder = 'Type to search...'})
{
	const [q, setQ] = useState('')
	const [list, setList] = useState([])
	const [open, setOpen] = useState(false)
	const [activeIndex, setActiveIndex] = useState(0)	
	const timer = useRef()
	const boxRef = useRef()
	const inputRef = useRef()

	const ensureListLoaded = async () =>
	{
		if(list.length === 0)
		{
			try
			{
				const { data } = await searchProducts('')
				setList(data || [])
				setActiveIndex(0)
			}

			catch(_) {}
		}

		setOpen(true)
	} 

	useEffect(() => {

		if(timer.current) clearTimeout(timer.current)
		if(q === '') return

		timer.current = setTimeout(async ()=>{

			try
			{
				const { data } = await searchProducts(q)
				setList(data || [])
				setActiveIndex(0)
				setOpen(true)
			}
			catch(_) 
			{

			}
		}, 250)
	}, [q])


	useEffect(()=> {

		const onClickOutside = (e) =>
		{
			if(boxRef.current && !boxRef.current.contains(e.target)) setOpen(false)
		}

		document.addEventListener('mousedown', onClickOutside)
		return () => document.removeEventListener('mousedown', onClickOutside)
	}, [])

	const pick = (p) =>
	{
		onSelect && onSelect(p)
		setQ('')
		setOpen(false)
		setList(prev => prev)		
	}

	const onKeyDown = (e) =>
	{	
		if(!open && (e.key === 'ArrowDown' || e.key === 'ArrowUp'))
		{
			ensureListLoaded()
			return
		}

		if(e.key === 'ArrowDown')
		{
			e.preventDefault()
			if(list.length) setActiveIndex((i) => (i + 1 ) % list.length)
		}

		else if( e.key === 'ArrowUp')
		{
			e.preventDefault()
			if(list.length) setActiveIndex((i) => (i - 1 + list.length ) % list.length)
		}

		else if (e.key === 'Enter')
		{
		 	if(open && list.length)
		 	{
		 		e.preventDefault()
		 		const p = list[activeIndex] || list[0]
		 		pick(p)

		 	}
		}

		else if( e.key === 'Escape')
		{
			setOpen(false)
		}
	}

	return (

				<div className="position-relative" ref={boxRef}>
					<label>Search By Name / SKU / barcode / ID</label>

					<div className="input-group">
						<input
							ref={inputRef} className= "form-control"
							placeholder={placeholder}
							value={q}
							onChange={(e)=>setQ(e.target.value)}
							onFocus={ensureListLoaded}
							onKeyDown={onKeyDown}
							autoComplete="off"
						 />

					<div className="input-group-append">
						<button
							type="button" className="btn btn-outline-secondary"
							onClick={ensureListLoaded}
							tabIndex={-1}
							aria-label="Show all products"
						>

						&#9660;
						</button>
					</div>
				</div>

				{open && (

					<div
						className="list-group position-absolute w-100"
						style={{ zIndex: 1000, maxHeight: 320, overflowY: 'auto' }}
						role="listbox"
					>

					{list.length === 0 && (

						<div className="list-group-item">No Products</div>

					)}

					{list.map((p, i) => (

						<button
							key={p.id}
							type="button"
							role="option"
							aria-selected={i === activeIndex}
							className={`list-group-item alist-group-item-action ${i === activeIndex ? 'active' : '' }`}onMouseEnter={()=>setActiveIndex(i)} onMouseDown={()=>pick(p)}
						>

						<div className="d-flex justify-content-between">
							<strong>{p.name}</strong>
							<small>&#2547;{p.price} &bull; {p.unit} &bull; Stock {p.stock}</small>
						</div>

						<small className="text-muted">
							SKU: {p.sku}{p.barcode ? ` &bull; Barcode: ${p.barcode}` : ''}
						</small>

						</button>
					))}

					</div>

				)}

				</div>
					
			)}

