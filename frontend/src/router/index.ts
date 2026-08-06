import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'dashboard', component: () => import('../views/DashboardView.vue') },
    { path: '/companies/:stockCode', name: 'company-profile', component: () => import('../views/CompanyProfileView.vue'), props: true },
    { path: '/screener', name: 'screener', component: () => import('../views/ScreenerView.vue') },
    { path: '/shariah', name: 'shariah-browse', component: () => import('../views/ShariahBrowseView.vue') },
    { path: '/watchlist', name: 'watchlist', component: () => import('../views/WatchlistView.vue'), meta: { requiresAuth: true } },
    { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
    { path: '/register', name: 'register', component: () => import('../views/RegisterView.vue') },
    { path: '/admin/shariah-import', name: 'admin-shariah-import', component: () => import('../views/AdminShariahImportView.vue'), meta: { requiresAdmin: true } },
  ],
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'dashboard' }
  }
  return true
})

export default router
