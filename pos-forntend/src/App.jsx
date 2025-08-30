/* eslint-disable no-unused-vars */
import React, { useState } from 'react';
import { Routes, Route, Like, useNavigate, Navigate } from 'react-router-dom';
import axios from 'axios';

import Login from './components/auth/Login';

function App() {
  const [token, setToken] = useState(localStorage.getItem('token') || '');
  const [user, setUser] = useState(null);

  const BASE_URL = import.meta.env.VITE_API_URL;

  const fetchProfile = async () => {
    try {
      const res = await axios.get(`${BASE_URL}api/user`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      setUser(res.data);
    } catch (err) {
      console.error(err.response);
      alert(err.response?.data?.message || 'Failed to fetch profile');
    }
  };

  const handleLogout = async () => {
    try {
      await axios.post(
        `${BASE_URL}api/logout`,
        {},
        {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );
      setToken('');
      setUser(null);
      localStorage.removeItem('token');
      alert('Logged out successfully!');
    } catch (err) {
      console.error(err.response);
    }
  };

  return (
    <div>
      {!token ? (
        <Login setToken={setToken} />
      ) : (
        <div>
          <button onClick={fetchProfile}>Get Profile</button>
          <button onClick={handleLogout}>Logout</button>
          {user && (
            <div>
              <h3>User Info</h3>
              <p>ID: {user.id}</p>
              <p>Name: {user.name}</p>
              <p>Username: {user.username}</p>
              <p>Email: {user.email}</p>
              <p>Phone: {user.phone}</p>
            </div>
          )}
        </div>
      )}
    </div>
  );
}

export default App;
