import axios from 'axios'
const tokenKey  = 'pos_token'

// Axios instance
export const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
    withCredentials: true, 
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
})

const saved = localStorage.getItem(tokenKey)
if (saved)
{
    api.defaults.headers.common['Authorization'] = `Bearer ${saved}`
}

// ===========================
// LOGIN
// ===========================
export const login = async (login, password) => {
    try {
        const res = await api.post('/api/login', { login, password })

        const token = res?.data?.token

        if (token)
        {
            localStorage.setItem(tokenKey, token)
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`
        }

        return res
    } catch (err) {
        console.error('Login error:', err)
        throw err
    }
}

// ===========================
// LOGOUT
// ===========================
export const logout = async () => {
    try {
        await api.post('/api/logout') 
    } catch (err) {
        console.error('Logout error:', err)
    } finally {
        localStorage.removeItem(tokenKey)
        delete api.defaults.headers.common['Authorization']
    }
}

// ===========================
// CREATE SALEbanglay bolo
// ===========================
export const createSale = async (payLoad) => {
    try {
        const res = await api.post('/api/sales', payLoad)
        return res.data
    } catch (err) {
        console.error('Create sale error:', err)
        throw err
    }
}

// ===========================
// EXAMPLE GET PRODUCTS
// ===========================
export const searchProducts = async (q) => {
    try {
        const res = await api.get('/api/products/search', { params: { q } })
        return res.data
    } catch (err) {
        console.error('Search error:', err)
        throw err
    }
}
