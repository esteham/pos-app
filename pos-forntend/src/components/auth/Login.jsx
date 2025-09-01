import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom'
import { login } from '../api/ApiAxios';

export default function LoginPage()
{
    const [loginInput, setLoginInput] = useState('');
    const [password, setPassword ] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const navigate = useNavigate();

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        try{
            await login(loginInput, password);
            navigate('/pos')
        }

        catch (err){
            setError(err?.response?.data?.message || 'Login Failed')
        }

        finally {
            setLoading(false)
        }

    }


    return (
        <div>
            <h2>Log in</h2>
            {error && <p>{error}</p>}

            <form action="" onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="">Email/Phone</label>
                    <input 
                        type="text"
                        value={loginInput}
                        onChange={(e) => setLoginInput(e.target.value)}
                    />
                </div>
                <div>
                    <label htmlFor="">Password</label>
                    <input 
                        type="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)} 
                    />
                </div>
                
                <button type='submit' disabled={loading}> 
                    { loading ? 'Logging In...' : 'Login' } 
                </button>
            </form>

        </div>
        
    );

}
