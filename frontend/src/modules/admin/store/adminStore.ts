import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAdminStore = defineStore('admin', () => {
  // Dummy Data for now
  const users = ref([
    { id: 1, name: 'Dr. Abebe Kebede', email: 'abebe@wou.edu.et', role: 'instructor', course_code: 'COMP301', status: 'active' },
    { id: 2, name: 'Fikirte Girma', email: 'fikirte@wou.edu.et', role: 'instructor', course_code: 'MATH201', status: 'active' },
    { id: 3, name: 'John Doe', email: 'john@student.wou.edu.et', role: 'student', course_code: null, status: 'active' },
  ])

  const departments = ref([
    { id: 1, name: 'Computer Science', code: 'COMP' },
    { id: 2, name: 'Mathematics', code: 'MATH' },
    { id: 3, name: 'Physics', code: 'PHYS' }
  ])

  const stats = ref({
    totalInstructors: 45,
    totalStudents: 1250,
    activeExams: 12,
    systemHealth: '100%'
  })

  return {
    users,
    departments,
    stats
  }
})
