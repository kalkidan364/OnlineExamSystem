import type { RouteRecordRaw } from 'vue-router'

export const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'Login',
    component: () => import('../modules/auth/pages/Login.vue')
  },
  {
    path: '/instructor',
    component: () => import('../layouts/DashboardLayout.vue'),
    // Note: In a real app we'd add meta: { requiresAuth: true, role: 'instructor' } here
    children: [
      {
        path: '',
        redirect: '/instructor/dashboard'
      },
      {
        path: 'dashboard',
        name: 'InstructorDashboard',
        component: () => import('../modules/instructor/pages/Dashboard.vue')
      },
      {
        path: 'question-banks',
        name: 'QuestionBanks',
        component: () => import('../modules/instructor/pages/QuestionBanks.vue')
      },
      {
        path: 'exams',
        name: 'Exams',
        component: () => import('../modules/instructor/pages/Exams.vue')
      },
      {
        path: 'schedule',
        name: 'ScheduleExams',
        component: () => import('../modules/instructor/pages/ScheduleExams.vue')
      },
      {
        path: 'students',
        name: 'StudentManagement',
        component: () => import('../modules/instructor/pages/Students.vue')
      },
      {
        path: 'results',
        name: 'Results',
        component: () => import('../modules/instructor/pages/Results.vue')
      },
      {
        path: 'reports',
        name: 'Reports',
        component: () => import('../modules/instructor/pages/Reports.vue')
      },
      {
        path: 'exams/create',
        name: 'CreateExam',
        component: () => import('../modules/instructor/pages/CreateExam.vue')
      },
      {
        path: 'profile',
        name: 'Profile',
        component: () => import('../modules/instructor/pages/Profile.vue')
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import('../modules/instructor/pages/Settings.vue')
      }
    ]
  }
]
