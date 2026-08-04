import { shallowRef } from 'vue'
import type { Editor } from '@tiptap/vue-3'

// The single source of truth: whichever editor is currently focused
const activeEditor = shallowRef<Editor | null>(null)
const activeFocusLabel = shallowRef<string>('')

export function useSharedEditor() {
  const setActiveEditor = (editor: Editor | null, label = '') => {
    activeEditor.value = editor
    activeFocusLabel.value = label
  }

  return {
    activeEditor,
    activeFocusLabel,
    setActiveEditor,
  }
}
