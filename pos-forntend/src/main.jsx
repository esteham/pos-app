import React from 'react'
import ReactDOM from 'react-dom/client'
import { BrowserRouter } from 'react-router-dom'
import 'bootstrap/dist/css/bootstrap.min.css';
import $ from 'jquery'
window.$ = window.jQuery = $
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import App from './App.jsx'

ReactDOM.createRoot(document.getElementById('root')).render(

  <React.StrictMode>
   <BrowserRouter>
	 <App />
   </BrowserRouter>
  </React.StrictMode>

)