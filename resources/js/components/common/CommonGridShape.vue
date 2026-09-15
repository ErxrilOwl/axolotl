<template>
  <svg
    class="absolute inset-0 -z-1 h-full w-full opacity-30"
    viewBox="0 0 480 480"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    preserveAspectRatio="xMidYMid slice"
  >
    <defs>
      <pattern id="grid-shape" width="40" height="40" patternUnits="userSpaceOnUse">
        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5" stroke-opacity="0.15" />
      </pattern>
      <radialGradient id="grid-fade" cx="50%" cy="50%" r="60%">
        <stop offset="0%" stop-color="white" stop-opacity="0.25" />
        <stop offset="100%" stop-color="white" stop-opacity="0" />
      </radialGradient>
    </defs>
    <rect width="480" height="480" fill="url(#grid-shape)" />
    <rect width="480" height="480" fill="url(#grid-fade)" />
  </svg>
</template>
