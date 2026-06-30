const fs = require('fs');
const path = require('path');

const componentsDir = path.join(__dirname, 'src/modules/student/components');
const files = fs.readdirSync(componentsDir).filter(f => f.endsWith('.vue'));

for (const file of files) {
  const filePath = path.join(componentsDir, file);
  let content = fs.readFileSync(filePath, 'utf8');
  content = content.replace(/from '\.\.\/\.\.\/types'/g, "from '../types'");
  
  // Fix Header.vue alert
  if (file === 'Header.vue') {
    content = content.replace(/@click="alert\(/g, '@click="windowRef.alert(');
    content = content.replace(/<script setup lang="ts">/, '<script setup lang="ts">\nconst windowRef = window;');
  }
  
  fs.writeFileSync(filePath, content);
}

// Fix mockData.ts import
const mockDataPath = path.join(__dirname, 'src/modules/student/data/mockData.ts');
let mockDataContent = fs.readFileSync(mockDataPath, 'utf8');
mockDataContent = mockDataContent.replace(/import {/g, 'import type {');
fs.writeFileSync(mockDataPath, mockDataContent);

// Fix ExamConsole.vue
const examConsolePath = path.join(__dirname, 'src/modules/student/components/ExamConsole.vue');
let examConsoleContent = fs.readFileSync(examConsolePath, 'utf8');
examConsoleContent = examConsoleContent.replace(/questions\.filter\(q => isQuestionAnswered\(q\.id\)\)/g, 'questions.filter((q: any) => isQuestionAnswered(q.id))');
fs.writeFileSync(examConsolePath, examConsoleContent);

// Fix Dashboard.vue
const dashboardPath = path.join(__dirname, 'src/modules/student/views/Dashboard.vue');
let dashboardContent = fs.readFileSync(dashboardPath, 'utf8');
dashboardContent = dashboardContent.replace(/window\.scrollTo/g, 'windowRef.scrollTo');
dashboardContent = dashboardContent.replace(/alert\(/g, 'windowRef.alert(');
// Insert windowRef definition
if (!dashboardContent.includes('const windowRef = window')) {
  dashboardContent = dashboardContent.replace(/const isNotificationsOpen = ref<boolean>\(false\)/, `const isNotificationsOpen = ref<boolean>(false)\nconst windowRef = window`);
}
fs.writeFileSync(dashboardPath, dashboardContent);

console.log('Fixes applied successfully');
