import type { RouteRecordRaw } from 'vue-router'

export const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'Login',
    component: () => import('../modules/auth/pages/Login.vue')
  },

  // ── Instructor Routes ──
  {
    path: '/instructor',
    component: () => import('../layouts/DashboardLayout.vue'),
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
        path: 'question-banks/:id',
        name: 'QuestionBankDetails',
        component: () => import('../modules/instructor/pages/QuestionBankDetails.vue')
      },
      {
        path: 'question-banks/:id/create-question',
        name: 'CreateQuestion',
        component: () => import('../modules/instructor/pages/CreateQuestion.vue')
      },
      {
        path: 'question-banks/:id/edit-question/:questionId',
        name: 'EditQuestion',
        component: () => import('../modules/instructor/pages/CreateQuestion.vue')
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
  },

  // ── Student Routes ──
  {
    path: '/student',
    name: 'StudentDashboard',
    component: () => import('../modules/student/views/Dashboard.vue')
  },

  // ── Super Admin Routes ──
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: () => import('../modules/admin/pages/Dashboard.vue')
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: () => import('../modules/admin/pages/Users.vue')
      },
      {
        path: 'instructors',
        name: 'AdminInstructors',
        component: () => import('../modules/admin/pages/Instructors.vue')
      },
      {
        path: 'students',
        name: 'AdminStudents',
        component: () => import('../modules/admin/pages/Students.vue')
      },
      {
        path: 'courses',
        name: 'AdminCourses',
        component: () => import('../modules/admin/pages/Courses.vue')
      },
      {
        path: 'exams',
        name: 'AdminExams',
        component: () => import('../modules/admin/pages/Exams.vue')
      },
      {
        path: 'question-banks',
        name: 'AdminQuestionBanks',
        component: () => import('../modules/admin/pages/QuestionBanks.vue')
      },
      {
        path: 'reports',
        name: 'AdminReports',
        component: () => import('../modules/admin/pages/Reports.vue')
      },
      {
        path: 'departments',
        name: 'AdminDepartments',
        component: () => import('../modules/admin/pages/Departments.vue')
      },
      {
        path: 'settings',
        name: 'AdminSettings',
        component: () => import('../modules/admin/pages/Settings.vue')
      }
    ]
  },

  // ── Department Head Routes ──
  {
    path: '/dept-head',
    component: () => import('../layouts/DeptHeadLayout.vue'),
    children: [
      {
        path: '',
        redirect: '/dept-head/dashboard'
      },
      {
        path: 'dashboard',
        name: 'DeptHeadDashboard',
        component: () => import('../modules/department-head/pages/Dashboard.vue')
      },
      {
        path: 'instructors',
        name: 'DeptHeadInstructors',
        component: () => import('../modules/department-head/pages/Instructors.vue')
      },
      {
        path: 'students',
        name: 'DeptHeadStudents',
        component: () => import('../modules/department-head/pages/Students.vue')
      },
      {
        path: 'courses',
        name: 'DeptHeadCourses',
        component: () => import('../modules/department-head/pages/Courses.vue')
      },
      {
        path: 'exams',
        name: 'DeptHeadExams',
        component: () => import('../modules/department-head/pages/Exams.vue')
      },
      {
        path: 'reports',
        name: 'DeptHeadReports',
        component: () => import('../modules/department-head/pages/Reports.vue')
      },
      {
        path: 'settings',
        name: 'DeptHeadSettings',
        component: () => import('../modules/department-head/pages/Settings.vue')
      }
    ]
  }
]
