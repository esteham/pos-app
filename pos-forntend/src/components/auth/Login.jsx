import React, { useState } from 'react';
import axios from 'axios';

function Login ({ setToken }){
    const [login, setLogin] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const BASE_URL = import.meta.env.VITE_API_URL;

    const handleSubmit = async (e) =>{
        e.preventDefault();
        try{
            const response = await axios.post(`${BASE_URL}api/login`,{
                login,
                password
            });
            // console.log(response.data);
            setToken(response.data.token);
            localStorage.setItem('token', response.data.token);

            setError('');
            alert('Login Successfull!');    
        }
        catch(err){
            console.error(err.response);
            setError(err.response?.data?.message || 'Login failed')
        }

    };

    return (
        <div>
            <h2>Log in</h2>
            {error && <p>{error}</p>}

            <form action="" onSubmit={handleSubmit}>
                <div>
                    <label htmlFor="">Email/Phone</label>
                    <input 
                        type="text"
                        value={login}
                        onChange={(e) => setLogin(e.target.value)}
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
                
                <button type='submit'>Login </button>
            </form>

        </div>
        
    );

}

export default Login;