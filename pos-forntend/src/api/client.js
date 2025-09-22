import axios from 'axios'
const tokenKey = 'pos_token'

export const api = axios.create({

		baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
		withCredentials: false,
		headers: { 'X-Requested-with': 'XMLHttpRequest' }
})

const saved = localStorage.getItem(tokenKey)
if(saved)
{
	api.defaults.headers.common['Authorization'] = `Bearer ${saved}`
}

export const login = async (email, password) => {

	const res = await api.post('/api/login', { email, password})

	const token = res?.data?.token

	if(token)
	{
		localStorage.setItem(tokenKey, token)
		api.defaults.headers.common['Authorization'] = `Bearer ${token}`
	}

	if(res?.data?.user)
	{
		localStorage.setItem('pos_user', JSON.stringify(res.data.user))
	}

	return res
}

//fetch category
export async function getCategories()
{
	const res = await api.get('/api/admin/categories');
	return res.data;
}

// create category
export const createCategory = (payload) => api.post('/api/admin/categories', payload)

// update category
export const updateCategory = (id, payload) => api.put(`/api/admin/categories/${id}`, payload)

// delete category
export const deleteCategory = (id) => api.delete(`/api/admin/categories/${id}`)

export const addStock = (payload) => api.post('/api/stock/adjust', payload)
export const getTodayReport = () => api.get('/api/reports/today')
export const getTopSales = (days = 7) =>  api.get('/api/reports/top-sales', { params: { days }})
export const getSalesReport = (params) => api.get('/api/reports/sales', { params })

export const listProducts = (params={}) => api.get('/api/admin/products', {params})
export const getProduct = (id) => api.get(`/api/admin/products/${id}`)

export const createProduct = (formData) =>
	api.post('/api/admin/products', formData, {

		headers: {'Content-Type': 'multipart/form-data'},
	})

export const updateProduct = (id, formData) => {

	let fd = formData instanceof FormData ? formData : new FormData()

	if(!(formData instanceof FormData))
	{
		Object.entries(formData || {}).forEach(([k, v]) => fd.append(k, v))
	}

	fd.set('_method','PUT')

	return api.post(`/api/admin/products/${id}`, fd, {
		headers: { 'Content-Type' : 'multipart/form-data' },
	})
}

export const deleteProduct = (id) => api.delete(`/api/admin/products/${id}`)

//-----Suppliers--------//

export const listSuppliers = (params={}) => api.get('api/admin/suppliers', {params})

export const getSupplier = (id) => api.get(`api/admin/suppliers/${id}`)

export const createSupplier = (payload) => api.post('api/admin/suppliers', payload)

export const updateSupplier = (id, payload) => {
	const fd = new FormData();
	Object.entries(payload).forEach(([k, v])=>fd.append(k, v))
	fd.set('_method','PUT')
	return api.post(`/api/admin/suppliers/${id}`, fd)
}

export const deleteSupplier = (id) => api.delete(`/api/admin/suppliers/${id}`)

//---------Purchase API-----------//

export const listPurchase = (params={}) => api.get('/api/admin/purchases', {params})

export const createPurchase = (payload) => api.post('/api/admin/purchases', payload)

//---------Purchase Summary API-----------//

export const getPurchaseSummary = (params = {}) => api.get('/api/admin/purchases/summary', {params})

//---------Supplier Payments API-----------//

export const createSupplierPayment = (payload) => api.post('/api/admin/supplier-payments', payload)

export const logout = () =>
{
	localStorage.removeItem(tokenKey)
	delete api.defaults.headers.common['Authorization']

	
}

export const searchProducts = (q) =>
	api.get('/api/products/search', {params: { q } })

export const createSale = (payload) =>	
	api.post('/api/sales', payload)

export const findCustomerByPhone = (phone) => 
  api.get('/api/customers/find', { params: { phone } })