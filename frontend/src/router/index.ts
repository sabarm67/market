import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'dashboard', component: () => import('../views/DashboardView.vue') },
    { path: '/companies', name: 'companies', component: () => import('../views/CompaniesView.vue') },
    { path: '/companies/:stockCode', name: 'company-profile', component: () => import('../views/CompanyProfileView.vue'), props: true },
    { path: '/screener', name: 'screener', component: () => import('../views/ScreenerView.vue') },
    { path: '/shariah', name: 'shariah-browse', component: () => import('../views/ShariahBrowseView.vue') },
    { path: '/watchlist', name: 'watchlist', component: () => import('../views/WatchlistView.vue'), meta: { requiresAuth: true } },
    { path: '/alerts', name: 'alerts', component: () => import('../views/AlertsView.vue'), meta: { requiresAuth: true } },
    { path: '/portfolio', name: 'portfolio', component: () => import('../views/PortfolioView.vue'), meta: { requiresAuth: true } },
    { path: '/login', name: 'login', component: () => import('../views/LoginView.vue') },
    { path: '/register', name: 'register', component: () => import('../views/RegisterView.vue') },
    { path: '/admin/shariah-import', name: 'admin-shariah-import', component: () => import('../views/AdminShariahImportView.vue'), meta: { requiresAdmin: true } },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  // On a hard reload / direct link, this guard can run before the initial session check
  // resolves — without waiting here, a valid session would still get bounced to /login
  // (isAuthenticated reads stale `null` state until fetchUser() finishes).
  if (to.meta.requiresAuth || to.meta.requiresAdmin) {
    await auth.ensureLoaded()
  }
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: 'dashboard' }
  }
  return true
})

export default router
