<script setup lang="ts">
import { ref } from 'vue'
import { useSharedEditor } from '../../composables/useSharedEditor'

const { activeEditor, activeFocusLabel } = useSharedEditor()

const isFullscreen = ref(false)
const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value
  document.body.style.overflow = isFullscreen.value ? 'hidden' : ''
}

const addLink = () => {
  if (!activeEditor.value) return
  const url = window.prompt('Enter URL:')
  if (url) activeEditor.value.chain().focus().setLink({ href: url }).run()
}
const addImage = () => {
  if (!activeEditor.value) return
  const url = window.prompt('Enter image URL:')
  if (url) activeEditor.value.chain().focus().setImage({ src: url }).run()
}
const addTable = () => {
  if (!activeEditor.value) return
  activeEditor.value.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
}

const btn = (active: boolean) =>
  `w-7 h-7 flex items-center justify-center rounded-lg text-[12px] font-semibold transition-all duration-150 ${
    active
      ? 'bg-[#4F46E5] text-white shadow-sm'
      : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800'
  }`
const iconBtn = (active: boolean) =>
  `p-1.5 rounded-lg transition-all duration-150 ${
    active
      ? 'bg-[#4F46E5] text-white shadow-sm'
      : 'text-slate-500 hover:bg-slate-100 hover:text-slate-800'
  }`
</script>

<template>
  <div
    :class="[
      'shared-toolbar border border-slate-200 rounded-2xl overflow-hidden transition-all duration-300',
      isFullscreen 
        ? 'fixed inset-0 z-[200] m-0 rounded-none flex flex-col bg-white' 
        : 'sticky top-[112px] z-[40] bg-white/90 backdrop-blur-md shadow-[0_8px_30px_rgb(0,0,0,0.06)] border-slate-200/60'
    ]"
  >
    <!-- Toolbar Header -->
    <div class="flex items-center justify-between px-4 py-2.5 bg-gradient-to-r from-slate-50/80 to-white/80 border-b border-slate-100/80">
      <div class="flex items-center gap-2">
        <div class="w-1.5 h-5 bg-gradient-to-b from-[#4F46E5] to-[#6366F1] rounded-full shadow-sm"></div>
        <span class="text-[12px] font-bold text-slate-700 tracking-wide">
          <span v-if="activeFocusLabel" class="text-[#4F46E5]">Editing:</span>
          {{ activeFocusLabel || 'Rich Text Editor' }}
        </span>
      </div>
      <div class="flex items-center gap-1">
        <!-- Undo -->
        <button type="button" @click="activeEditor?.chain().focus().undo().run()" :disabled="!activeEditor" title="Undo" :class="iconBtn(false)">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5v0a5.5 5.5 0 0 1-5.5 5.5H11"/></svg>
        </button>
        <!-- Redo -->
        <button type="button" @click="activeEditor?.chain().focus().redo().run()" :disabled="!activeEditor" title="Redo" :class="iconBtn(false)">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 14 5-5-5-5"/><path d="M20 9H9.5A5.5 5.5 0 0 0 4 14.5v0A5.5 5.5 0 0 0 9.5 20H13"/></svg>
        </button>
        <div class="w-px h-4 bg-slate-200 mx-1"></div>
        <!-- Fullscreen -->
        <button type="button" @click="toggleFullscreen" :class="iconBtn(isFullscreen)" :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">
          <svg v-if="!isFullscreen" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
          <svg v-else class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"/></svg>
        </button>
      </div>
    </div>

    <!-- Toolbar Buttons Row -->
    <div class="flex flex-wrap items-center gap-x-0.5 gap-y-1 px-3 py-2 border-b border-slate-100 bg-white">

      <!-- Headings -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().toggleHeading({ level: 1 }).run()" :disabled="!activeEditor"
          :class="btn(!!activeEditor?.isActive('heading', { level: 1 }))" title="Heading 1">H₁</button>
        <button type="button" @click="activeEditor?.chain().focus().toggleHeading({ level: 2 }).run()" :disabled="!activeEditor"
          :class="btn(!!activeEditor?.isActive('heading', { level: 2 }))" title="Heading 2">H₂</button>
        <button type="button" @click="activeEditor?.chain().focus().toggleHeading({ level: 3 }).run()" :disabled="!activeEditor"
          :class="btn(!!activeEditor?.isActive('heading', { level: 3 }))" title="Heading 3">H₃</button>
      </div>

      <!-- Basic Formatting -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().toggleBold().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('bold'))" title="Bold">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 12a4 4 0 0 0 0-8H6v8"/><path d="M15 20a4 4 0 0 0 0-8H6v8Z"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleItalic().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('italic'))" title="Italic">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" x2="10" y1="4" y2="4"/><line x1="14" x2="5" y1="20" y2="20"/><line x1="15" x2="9" y1="4" y2="20"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleUnderline().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('underline'))" title="Underline">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 4v6a6 6 0 0 0 12 0V4"/><line x1="4" x2="20" y1="20" y2="20"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleStrike().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('strike'))" title="Strikethrough">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 4H9a3 3 0 0 0-2.83 4"/><path d="M14 12a4 4 0 0 1 0 8H6"/><line x1="4" x2="20" y1="12" y2="12"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleHighlight().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('highlight'))" title="Highlight">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 11-6 6v3h9l3-3"/><path d="m22 12-4.6 4.6a2 2 0 0 1-2.8 0l-5.2-5.2a2 2 0 0 1 0-2.8L14 4"/></svg>
        </button>
      </div>

      <!-- Superscript / Subscript -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().toggleSuperscript().run()" :disabled="!activeEditor"
          :class="btn(!!activeEditor?.isActive('superscript'))" title="Superscript">x²</button>
        <button type="button" @click="activeEditor?.chain().focus().toggleSubscript().run()" :disabled="!activeEditor"
          :class="btn(!!activeEditor?.isActive('subscript'))" title="Subscript">x₂</button>
      </div>

      <!-- Lists -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().toggleBulletList().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('bulletList'))" title="Bullet List">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleOrderedList().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('orderedList'))" title="Numbered List">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleBlockquote().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('blockquote'))" title="Quote">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
        </button>
      </div>

      <!-- Alignment -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().setTextAlign('left').run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive({ textAlign: 'left' }))" title="Align Left">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" x2="3" y1="6" y2="6"/><line x1="15" x2="3" y1="12" y2="12"/><line x1="17" x2="3" y1="18" y2="18"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().setTextAlign('center').run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive({ textAlign: 'center' }))" title="Align Center">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" x2="3" y1="6" y2="6"/><line x1="17" x2="7" y1="12" y2="12"/><line x1="19" x2="5" y1="18" y2="18"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().setTextAlign('right').run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive({ textAlign: 'right' }))" title="Align Right">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="21" x2="3" y1="6" y2="6"/><line x1="21" x2="9" y1="12" y2="12"/><line x1="21" x2="7" y1="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Code & Special -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="activeEditor?.chain().focus().toggleCode().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('code'))" title="Inline Code">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().toggleCodeBlock().run()" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('codeBlock'))" title="Code Block">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
        </button>
        <button type="button" @click="activeEditor?.chain().focus().setHorizontalRule().run()" :disabled="!activeEditor"
          :class="iconBtn(false)" title="Horizontal Rule">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" x2="19" y1="12" y2="12"/></svg>
        </button>
      </div>

      <!-- Insert -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" @click="addLink" :disabled="!activeEditor"
          :class="iconBtn(!!activeEditor?.isActive('link'))" title="Insert Link">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </button>
        <button type="button" @click="addImage" :disabled="!activeEditor"
          :class="iconBtn(false)" title="Insert Image">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        </button>
        <button type="button" @click="addTable" :disabled="!activeEditor"
          :class="iconBtn(false)" title="Insert Table">
          <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18M3 15h18M9 3v18M15 3v18"/></svg>
        </button>
      </div>

      <!-- Math symbols quick insert -->
      <div class="flex items-center gap-0.5 pr-2 mr-1 border-r border-slate-200">
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert sqrt symbol"
          @click="activeEditor?.chain().focus().insertContent('√').run()">√</button>
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert pi"
          @click="activeEditor?.chain().focus().insertContent('π').run()">π</button>
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert sigma"
          @click="activeEditor?.chain().focus().insertContent('Σ').run()">Σ</button>
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert infinity"
          @click="activeEditor?.chain().focus().insertContent('∞').run()">∞</button>
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert integral"
          @click="activeEditor?.chain().focus().insertContent('∫').run()">∫</button>
        <button type="button" :disabled="!activeEditor" :class="btn(false)" title="Insert delta"
          @click="activeEditor?.chain().focus().insertContent('Δ').run()">Δ</button>
      </div>

      <!-- Clear formatting -->
      <button type="button" @click="activeEditor?.chain().focus().clearNodes().unsetAllMarks().run()" :disabled="!activeEditor"
        :class="iconBtn(false)" title="Clear Formatting">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V4h16v3"/><path d="M5 20h6"/><path d="M13 4l-8 16"/><line x1="18" y1="12" x2="22" y2="16"/><line x1="22" y1="12" x2="18" y2="16"/></svg>
      </button>
    </div>

    <!-- No editor focused state -->
    <div v-if="!activeEditor" class="px-4 py-2 text-[11px] text-slate-400 italic text-center">
      Click on a text field below to start editing
    </div>

    <!-- Slot for content (when fullscreen) -->
    <div v-if="isFullscreen" class="flex-1 overflow-auto p-6 bg-white">
      <slot name="fullscreen-content" />
    </div>
  </div>
</template>
