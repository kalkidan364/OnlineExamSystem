/**
 * useStudentProfile — fetches the authenticated student's real profile from the backend
 * and maps it onto the StudentProfile shape used by student components.
 */
import { ref, computed } from 'vue'
import { useAuthStore } from '../../auth/store/authStore'
import apiClient from '../../../core/api/apiClient'
import type { StudentProfile } from '../types'

/** Default avatar when no photo is set */
const DEFAULT_AVATAR =
  'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=256'

/**
 * Maps a raw backend user object → StudentProfile shape.
 */
function mapToProfile(user: any): StudentProfile {
  const department =
    (user.department?.name ?? user.department ?? 'Software Engineering') as string

  return {
    name: user.name ?? 'Student',
    id: user.student_id ?? user.username ?? `WU/${user.id}/18`,
    email: user.email ?? '',
    department,
    program: user.program ?? 'Bachelor of Science (B.Sc.)',
    semester: user.semester ?? 'Semester II',
    academicYear: user.academic_year ?? '2025/2026 Academic Year',
    avatar: user.avatar ?? user.profile_photo_url ?? DEFAULT_AVATAR,
    cgpa: Number(user.cgpa ?? 0) || 0,
    creditsCompleted: Number(user.credits_completed ?? 0) || 0,
  }
}

export function useStudentProfile() {
  const authStore = useAuthStore()
  const isFetching = ref(false)
  const fetchError = ref<string | null>(null)

  // Start from authStore.user (set immediately after login) so there is
  // never a blank state while the network call is in flight.
  const profile = ref<StudentProfile>(
    authStore.user ? mapToProfile(authStore.user) : mapToProfile({})
  )

  /**
   * Fetch the latest profile from the backend (/v1/user) and
   * merge it back into both the local ref and the auth store.
   */
  const fetchProfile = async () => {
    // If there is no token the student is not logged in – skip silently.
    const token = localStorage.getItem('auth_token')
    if (!token) return

    isFetching.value = true
    fetchError.value = null

    try {
      const response = await apiClient.get('/user')
      const serverUser = response.data

      // Keep authStore.user in sync so other parts of the app also see the update
      authStore.user = { ...authStore.user, ...serverUser }

      profile.value = mapToProfile(serverUser)
    } catch (err: any) {
      // Backend unreachable → stick with what we got from the login payload
      fetchError.value = err.message ?? 'Failed to fetch profile'
      console.warn('[useStudentProfile] Using cached login data:', fetchError.value)

      // Still map from authStore.user so the UI isn't blank
      if (authStore.user) {
        profile.value = mapToProfile(authStore.user)
      }
    } finally {
      isFetching.value = false
    }
  }

  return {
    profile,
    isFetching,
    fetchError,
    fetchProfile,
  }
}
