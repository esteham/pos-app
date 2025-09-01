import React from 'react'

export default function CustomerPhoneInput({ value, onChange })
{
	const set = (patch) => onChange({ ...value, ...patch })

	return (

			<>

				<div className="form-group">
					<label>Customer Phone</label>
					<input className="form-ontrol" value={value.phone} onChange={(e)=>set({ phone: e.target.value })} placeholder="e.g. 017000000" />
				</div>

				<div className="form-group">
					<label>Customer Name (optional)</label>
					<input className="form-ontrol" value={value.name} onChange={(e)=>set({ name: e.target.value })} />
				</div>
			</>
		)
}