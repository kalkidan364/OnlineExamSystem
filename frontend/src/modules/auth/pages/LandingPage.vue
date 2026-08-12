<script setup lang="ts">
import { useRouter } from 'vue-router'
import landingBg from '@/assets/images/image.png'

const router = useRouter()
const goToPortal = () => router.push('/login')
</script>

<template>
  <div style="position:fixed;top:0;left:0;width:100vw;height:100vh;overflow:hidden;background:#f0f0f0;">

    <!-- Background image — stretches to fill entire viewport -->
    <img
      :src="landingBg"
      alt=""
      style="
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: fill;
        display: block;
      "
      aria-hidden="true"
    />

    <!-- ── TOP HEADER BAR ── -->
    <div style="
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1.2rem 2.5rem;
      z-index: 20;
    ">
      <!-- Wollo University Logo (top-left) -->
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <img
          src="/src/assets/images/logo.png"
          alt="Wollo University"
          style="
            height: 56px;
            width: 56px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 12px rgba(0,0,0,0.18);
            border: 2px solid rgba(255,255,255,0.6);
          "
        />
        <span style="
          font-family: 'Segoe UI', sans-serif;
          font-size: 0.95rem;
          font-weight: 800;
          color: #1a1a2e;
          letter-spacing: 0.2px;
          line-height: 1.3;
        ">Wollo University</span>
      </div>

      <!-- Login Button (top-right) -->
      <button
        @click="goToPortal"
        style="
          background: #3b82f6;
          color: white;
          font-family: 'Segoe UI', sans-serif;
          font-size: 0.88rem;
          font-weight: 700;
          padding: 0.65rem 1.8rem;
          border-radius: 50px;
          border: none;
          cursor: pointer;
          box-shadow: 0 4px 16px rgba(59,130,246,0.35);
          transition: background 0.2s, transform 0.15s;
          letter-spacing: 0.3px;
        "
        onmouseover="this.style.background='#2563eb'; this.style.transform='scale(1.05)'"
        onmouseout="this.style.background='#3b82f6'; this.style.transform='scale(1)'"
      >
        Login
      </button>
    </div>

    <div style="
      position: absolute;
      top: 0;
      left: 0;
      width: 42%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      padding: 0 0 0 5%;
      z-index: 10;
    ">
      <!-- Title -->
      <h1 style="
        font-family: 'Segoe UI', sans-serif;
        font-size: clamp(4.6rem, 5.8vw, 4.8rem);
        font-weight: 700;
        color: #1a1a2e;
        line-height: 1.2;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
      ">
        Online 
      </h1>
       <h1 style="
        font-family: 'Segoe UI', sans-serif;
        font-size: clamp(1.6rem, 2.8vw, 2.8rem);
        font-weight: 500;
        color: #1a1a2e;
        line-height: 1.2;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
      ">
        Exam System
      </h1>
<p style="margin-bottom: 2rem;">Access your examination securely and manage your academic <br/> journey in one place</p>
      <!-- Continue to Portal Button -->
      <button
        @click="goToPortal"
        style="
          background: #3b82f6;
          color: white;
          font-family: 'Segoe UI', sans-serif;
          font-size: 0.95rem;
          font-weight: 700;
          padding: 0.85rem 2.2rem;
          border-radius: 50px;
          border: none;
          cursor: pointer;
          box-shadow: 0 4px 20px rgba(59,130,246,0.4);
          transition: background 0.2s, transform 0.15s;
          letter-spacing: 0.3px;
        "
        onmouseover="this.style.background='#2563eb'; this.style.transform='scale(1.04)'"
        onmouseout="this.style.background='#3b82f6'; this.style.transform='scale(1)'"
      >
        Continue to Portal →
      </button>
    </div>

  </div>
</template>
