import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { login } from '../api/client.js'

export default function LoginPage()
{
	const [email, setEmail] = useState('cashier@pos.local')
	const [password, setPassword] = useState('cashier123')
	const [loading, setLoading] = useState(false)
	const [error, setError] = useState('')
	const navigate = useNavigate()

	const onSubmit = async (e) =>
	{	
		e.preventDefault()
		setLoading(true);
		setError()

		try
		{
			await login(email, password)
			// Decide redirect based on role
			let user
			try { user = JSON.parse(localStorage.getItem('pos_user') || 'null') } catch {}
			const rawRole = ((user && (user.role ?? user.user_type ?? user.type)) || '')
				.toString()
				.toLowerCase()
			const isAdmin = ['admin','manager','super admin','super_admin','superadmin'].includes(rawRole)
			navigate(isAdmin ? '/admin' : '/pos')			
		}

		catch(err)
		{
			setError(err?.response?.data?.message || 'Login Failed')
		}
		finally
		{
			setLoading(false)
		}
	}

	return (

			<div className="row justify-content-center">

				<div className="col-md-4">
					<div className="card">
						<div className="card-header">
						Login
						</div>
						<div className="card-body">
							{error && <div className="alert alert-danger">{error}</div>}

							<form onSubmit={onSubmit}>

								<div className="form-group">
									<label>Email</label>
									<input className="form-control" value={email} onChange={(e)=>setEmail(e.target.value)} />
								</div>

								<div className="form-group">
									<label>Password</label>
									<input type="password" className="form-control" value={password} onChange={(e)=>setPassword(e.target.value)} />
								</div>

								<button className="btn btn-primary btn-block" disabled={loading}> { loading ? 'Logging In...':'Login' }
								</button>
							</form>
						</div>
					</div>
					<p className="text-muted mt-2">Use the seeded user (e.g. cashier@pos.local / cashier123)
					</p>
				</div>
			</div>
		  )

}