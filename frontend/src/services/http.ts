import axios from 'axios';

const http = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Interceptor de Requisição: Antes de enviar qualquer chamada, ele verifica se tem token
http.interceptors.request.use(config => {
    const token = localStorage.getItem('access_token');
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
}, error => {
    return Promise.reject(error);
});

export default http;