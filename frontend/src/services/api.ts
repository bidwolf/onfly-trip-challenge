import axios, { type AxiosInstance, type AxiosResponse, type InternalAxiosRequestConfig } from 'axios'

const API_URL = import.meta.env.VITE_API_URL as string

const api: AxiosInstance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})
api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
api.interceptors.request.use(
  (config: InternalAxiosRequestConfig): InternalAxiosRequestConfig => {
    const token = localStorage.getItem('token')
    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => Promise.reject(error),
)

api.interceptors.response.use(
  (response: AxiosResponse) => response,
  async (error) => {
    const originalRequest = (error.config as InternalAxiosRequestConfig & { _retry?: boolean })

    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true

      try {
        const { data } = await axios.post<{ token: string }>(`${API_URL}/auth/refresh`, {}, {
          headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        if (!data.token) {
          localStorage.removeItem('token')
          return Promise.reject(error)
        }
        localStorage.setItem('token', data.token)
        if (originalRequest.headers) {
          originalRequest.headers.Authorization = `Bearer ${data.token}`
        }
        return api(originalRequest)
      } catch {
        localStorage.removeItem('token')
        return Promise.reject(error)
      }
    }

    return Promise.reject(error)
  },
)

export default api