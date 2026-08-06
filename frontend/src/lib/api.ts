import axios from 'axios'

// In production this is empty ("" from frontend/.env.production), so requests are
// relative and same-origin (SPA is served by Laravel itself — see routes/web.php).
// In dev it falls back to the local Laravel dev server on a different port.
const API_BASE = import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000'

export const api = axios.create({
  baseURL: `${API_BASE}/api/v1`,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: 'application/json',
  },
})

export async function ensureCsrfCookie() {
  await axios.get(`${API_BASE}/sanctum/csrf-cookie`, { withCredentials: true })
}
