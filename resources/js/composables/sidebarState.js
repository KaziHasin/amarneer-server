import { ref } from 'vue'

export const isSidebarCollapsed = ref(false)

export function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}

