import React, { useEffect, useRef } from 'react'
import { findCustomerByPhone } from '../api/client'

export default function CustomerPhoneInput({ value, onChange }) 
{
  const timer = useRef()

  const set = (patch) => onChange({ ...value, ...patch })

  useEffect(() => {
    
    if (timer.current) clearTimeout(timer.current)
    if (!value.phone || String(value.phone).length < 7) return
    timer.current = setTimeout(async () => 
    {       
      try 
      {
        const { data } = await findCustomerByPhone(value.phone)
        if (data?.found && data.customer) {
          set({ name: data.customer.name || '', email: data.customer.email || '' })
        }
      } 
      catch (_) {}
    }, 400)    
  }, [value.phone])

  return (
    <>
      <div className="form-group">
        <label>Customer Phone</label>
        <input className="form-control" value={value.phone || ''}
          onChange={(e)=>set({ phone: e.target.value })} placeholder="e.g., 017XXXXXXXX" />
      </div>
      <div className="form-group">
        <label>Customer Name</label>
        <input className="form-control" value={value.name || ''}
          onChange={(e)=>set({ name: e.target.value })} />
      </div>
      <div className="form-group"> {/* [NEW] */}
        <label>Customer Email</label>
        <input className="form-control" type="email" value={value.email || ''}
          onChange={(e)=>set({ email: e.target.value })} placeholder="optional" />
      </div>
    </>
  )
}
