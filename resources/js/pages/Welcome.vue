<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  canLogin: { type: Boolean, default: true },
  canRegister: { type: Boolean, default: true },
  laravelVersion: { type: String, default: '13.x' },
  phpVersion: { type: String, default: '8.4' },
})

const installCommand = 'git clone https://github.com/ErxrilOwl/axolotl.git'
const copied = ref(false)

async function copyInstall() {
  try {
    await navigator.clipboard.writeText(installCommand)
    copied.value = true
    setTimeout(() => (copied.value = false), 2000)
  } catch {
    copied.value = false
  }
}

const tree = [
  { line: 'my-app/', kind: 'dir' },
  { line: '├─ app/Http/Controller/', kind: 'dir', note: 'all in one controller dir' },
  { line: '├─ app/Http/Models/', kind: 'dir', note: 'models live here' },
  { line: '├─ resources/js/Pages/', kind: 'dir', note: 'Inertia pages, typed' },
  { line: '├─ resources/js/Components/ui/', kind: 'dir', note: 'unstyled primitives' },
  { line: '├─ tests/Feature/', kind: 'dir', note: 'Pest, 400+ assertions' },
  { line: '└─ .github/workflows/ci.yml', kind: 'file', note: 'lint, analyse, test' },
]

const features = [
  {
    title: 'Accounts, finished',
    body: 'Registration, login, password reset, email verification and device sessions. Every path has a test next to it.',
    span: 'lg:col-span-7',
  },
  {
    title: 'Role Based Action Controller',
    body: 'Role CRUD and Permissions',
    span: 'lg:col-span-5',
  },
  {
    title: 'Data Import',
    body: 'Readily available imports.',
    span: 'lg:col-span-5',
  },
  {
    title: 'A frontend that knows your models',
    body: 'Inertia 2, Vue 3 and TypeScript. An artisan command writes interfaces for every Eloquent model and route, so props are typed all the way down.',
    span: 'lg:col-span-7',
  },
  {
    title: 'The unglamorous parts',
    body: 'Queues, Laravel Excel and AWS configured, scheduled tasks registered, and an image uploading',
    span: 'lg:col-span-6',
  },
]

const generated = [
  'app/Models/Invoice.php',
  'app/Policies/InvoicePolicy.php',
  'app/Http/Requests/InvoiceRequest.php',
  'database/migrations/2026_09_16_create_invoices_table.php',
  'database/factories/InvoiceFactory.php',
  'resources/js/Pages/Invoices/Index.vue',
  'resources/js/Pages/Invoices/Form.vue',
  'tests/Feature/InvoiceTest.php',
]
</script>

<template>
  <Head title="Axolotl — a Laravel and Inertia starter kit" />

  <div class="min-h-screen bg-[#EDF3F1] text-[#13302F] antialiased font-['Public_Sans',ui-sans-serif,system-ui]">
    <!-- Nav -->
    <header class="mx-auto flex max-w-6xl items-center justify-between px-6 py-6">
      <a href="/" class="flex items-center gap-2.5 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#E45C7F]">
        <svg viewBox="0 0 420 340" class="h-8 w-8" aria-hidden="true">
          <ellipse cx="210" cy="150" rx="84" ry="72" fill="#F09EB4" />
          <circle cx="58" cy="120" r="34" fill="#F09EB4" />
          <circle cx="362" cy="120" r="34" fill="#F09EB4" />
          <rect x="152" y="196" width="116" height="104" rx="52" fill="#F09EB4" />
          <circle cx="178" cy="148" r="8" fill="#13302F" />
          <circle cx="242" cy="148" r="8" fill="#13302F" />
        </svg>
        <span class="text-[19px] font-semibold tracking-[-0.02em] font-['Bricolage_Grotesque',ui-sans-serif]">Axolotl</span>
      </a>

      <nav class="flex items-center gap-1 text-[15px]">
        <template v-if="canLogin">
          <Link href="/signin" class="rounded-lg px-3 py-2 text-[#41635F] transition-colors hover:text-[#13302F] hover:bg-[#DCE8E5] focus-visible:outline focus-visible:outline-2 focus-visible:outline-[#E45C7F]">Log in</Link>
          <Link
            v-if="canRegister"
            href="/signup"
            class="ml-1 rounded-lg bg-[#13302F] px-4 py-2 font-medium text-[#EDF3F1] transition-colors hover:bg-[#1D4645] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E45C7F]"
          >Create an account</Link>
        </template>
      </nav>
    </header>

    <!-- Hero -->
    <section class="mx-auto grid max-w-6xl items-center gap-10 px-6 pt-10 pb-20 lg:grid-cols-12 lg:gap-6 lg:pt-16 lg:pb-28">
      <div class="lg:col-span-7">
        <h1 class="max-w-[13ch] text-[44px] font-semibold leading-[0.97] tracking-[-0.035em] sm:text-[58px] lg:text-[68px] font-['Bricolage_Grotesque',ui-sans-serif]">
          The first two weeks of every Laravel app, already written.
        </h1>

        <p class="mt-6 max-w-[62ch] text-[18px] leading-[1.6] text-[#41635F]">
          Axolotl is a Laravel {{ laravelVersion }} and Inertia starter kit with authentication,
          user, RBAC and a test suite already wired together. Clone it, rename it,
          and spend your first day on the part nobody else has built.
        </p>

        <!-- Install -->
        <div class="mt-9 flex max-w-xl items-center gap-2 rounded-2xl border border-[#C9DCD8] bg-white p-2 pl-4 shadow-[0_1px_0_rgba(19,48,47,0.04)]">
          <code class="flex-1 overflow-x-auto whitespace-nowrap py-2 text-[14px] text-[#13302F] font-['JetBrains_Mono',ui-monospace,monospace]">
            <span class="select-none text-[#8FAEA9]">$ </span>{{ installCommand }}
          </code>
          <button
            type="button"
            @click="copyInstall"
            class="shrink-0 rounded-xl bg-[#E45C7F] px-4 py-2.5 text-[14px] font-medium text-white transition-colors hover:bg-[#D24A6E] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#13302F]"
          >
            {{ copied ? 'Copied' : 'Copy' }}
          </button>
        </div>

        <p class="mt-4 text-[14px] text-[#5C7C78]">
          MIT licensed. Needs PHP {{ phpVersion }} and Node 20.
          <a href="/docs/installation" class="ml-1 text-[#13302F] underline decoration-[#A9C6C1] underline-offset-4 transition-colors hover:decoration-[#E45C7F]">Read the install guide</a>
        </p>
      </div>

      <!-- Mascot -->
      <div class="lg:col-span-5">
        <svg viewBox="0 0 420 340" class="mx-auto w-full max-w-[380px]" role="img" aria-label="Illustration of a smiling pink axolotl">
          <g class="gill gill--left" stroke="#EE93AB" stroke-width="10" stroke-linecap="round" fill="none">
            <path d="M132 118 C 104 106, 80 96, 62 92" />
            <path d="M126 150 C 98 148, 72 144, 52 142" />
            <path d="M132 182 C 106 194, 84 202, 66 208" />
          </g>
          <g class="gill gill--left" fill="#EE93AB">
            <circle cx="58" cy="90" r="14" /><circle cx="78" cy="99" r="9" />
            <circle cx="48" cy="141" r="14" /><circle cx="70" cy="144" r="9" />
            <circle cx="62" cy="209" r="14" /><circle cx="82" cy="199" r="9" />
          </g>

          <g class="gill gill--right" stroke="#EE93AB" stroke-width="10" stroke-linecap="round" fill="none">
            <path d="M288 118 C 316 106, 340 96, 358 92" />
            <path d="M294 150 C 322 148, 348 144, 368 142" />
            <path d="M288 182 C 314 194, 336 202, 354 208" />
          </g>
          <g class="gill gill--right" fill="#EE93AB">
            <circle cx="362" cy="90" r="14" /><circle cx="342" cy="99" r="9" />
            <circle cx="372" cy="141" r="14" /><circle cx="350" cy="144" r="9" />
            <circle cx="358" cy="209" r="14" /><circle cx="338" cy="199" r="9" />
          </g>

          <ellipse cx="140" cy="254" rx="21" ry="12" transform="rotate(-18 140 254)" fill="#F9C7D2" />
          <ellipse cx="280" cy="254" rx="21" ry="12" transform="rotate(18 280 254)" fill="#F9C7D2" />
          <rect x="152" y="196" width="116" height="106" rx="53" fill="#F9C7D2" />
          <ellipse cx="210" cy="262" rx="35" ry="28" fill="#FDE6EB" />
          <ellipse cx="210" cy="150" rx="84" ry="72" fill="#F9C7D2" />

          <circle cx="158" cy="174" r="13" fill="#F58FA6" opacity="0.5" />
          <circle cx="262" cy="174" r="13" fill="#F58FA6" opacity="0.5" />
          <circle cx="178" cy="148" r="8" fill="#13302F" />
          <circle cx="242" cy="148" r="8" fill="#13302F" />
          <circle cx="181" cy="145" r="2.6" fill="#FFFFFF" />
          <circle cx="245" cy="145" r="2.6" fill="#FFFFFF" />
          <path d="M192 170 q9 11 18 0 q9 11 18 0" stroke="#13302F" stroke-width="4.5" stroke-linecap="round" fill="none" />
        </svg>
      </div>
    </section>

    <!-- What's inside -->
    <section class="border-t border-[#D7E4E1]">
      <div class="mx-auto grid max-w-6xl gap-12 px-6 py-20 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-5">
          <h2 class="text-[32px] font-semibold leading-[1.1] tracking-[-0.03em] sm:text-[38px] font-['Bricolage_Grotesque',ui-sans-serif]">
            Opinionated where it saves you an argument
          </h2>
          <p class="mt-5 max-w-[52ch] text-[17px] leading-[1.65] text-[#41635F]">
            The structure is the part of a boilerplate you live with longest, so Axolotl
            commits to one: actions instead of service classes, policies instead of
            inline checks, and a component library you can restyle rather than fight.
          </p>
        </div>

        <div class="lg:col-span-7">
          <div class="rounded-2xl border border-[#C9DCD8] bg-white p-6 sm:p-7">
            <ul class="space-y-3 text-[13.5px] leading-[1.7] font-['JetBrains_Mono',ui-monospace,monospace]">
              <li v-for="item in tree" :key="item.line" class="flex flex-wrap items-baseline justify-between gap-x-6">
                <span :class="item.kind === 'dir' ? 'text-[#13302F]' : 'text-[#17756E]'">{{ item.line }}</span>
                <span v-if="item.note" class="text-[#8FAEA9]">{{ item.note }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="border-t border-[#D7E4E1]">
      <div class="mx-auto max-w-6xl px-6 py-20">
        <div class="grid gap-x-12 lg:grid-cols-12">
          <article
            v-for="(feature, i) in features"
            :key="feature.title"
            :class="[feature.span, i > 1 ? 'border-t border-[#D7E4E1] pt-8 mt-8 lg:mt-0' : '']"
            class="lg:border-t lg:border-[#D7E4E1] lg:pt-8 lg:mt-8 first:lg:mt-0 [&:nth-child(2)]:lg:mt-0"
          >
            <h3 class="text-[21px] font-semibold tracking-[-0.02em] font-['Bricolage_Grotesque',ui-sans-serif]">
              {{ feature.title }}
            </h3>
            <p class="mt-3 max-w-[58ch] text-[16.5px] leading-[1.65] text-[#41635F]">
              {{ feature.body }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- Generators -->
    <section class="bg-[#0F2A2A] text-[#DDEAE7]">
      <div class="mx-auto grid max-w-6xl gap-12 px-6 py-20 lg:grid-cols-12 lg:gap-16 lg:py-24">
        <div class="lg:col-span-5">
          <h2 class="text-[32px] font-semibold leading-[1.1] tracking-[-0.03em] text-white sm:text-[40px] font-['Bricolage_Grotesque',ui-sans-serif]">
            Axolotls regrow what they lose. So does your scaffolding.
          </h2>
          <p class="mt-5 max-w-[52ch] text-[17px] leading-[1.65] text-[#9FBDB8]">
            One command writes the model, migration, factory, policy, form request,
            Inertia pages and a passing test — all following the same conventions as
            everything already in the repo. Delete any of it and run the command again.
          </p>
        </div>

        <div class="lg:col-span-7">
          <div class="overflow-hidden rounded-2xl border border-[#245252] bg-[#08201F]">
            <div class="border-b border-[#245252] px-5 py-3 text-[13px] text-[#7FA5A0] font-['JetBrains_Mono',ui-monospace,monospace]">
              terminal
            </div>
            <pre class="overflow-x-auto px-5 py-5 text-[13.5px] leading-[1.9] font-['JetBrains_Mono',ui-monospace,monospace]"><span class="text-[#5C8A85]">$</span> <span class="text-white">php artisan axolotl:make Invoice</span>
<span v-for="path in generated" :key="path"><span class="text-[#F2A0B5]">created</span>  <span class="text-[#B9D2CE]">{{ path }}</span>
</span><span class="text-[#5C8A85]">8 files. Run `php artisan test` to see them pass.</span></pre>
          </div>
        </div>
      </div>
    </section>

    <!-- Closing -->
    <section class="border-t border-[#D7E4E1]">
      <div class="mx-auto max-w-6xl px-6 py-20 text-center lg:py-28">
        <h2 class="mx-auto max-w-[18ch] text-[36px] font-semibold leading-[1.05] tracking-[-0.03em] sm:text-[48px] font-['Bricolage_Grotesque',ui-sans-serif]">
          Start the project, not the setup
        </h2>
        <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
          <button
            type="button"
            @click="copyInstall"
            class="rounded-xl bg-[#E45C7F] px-6 py-3.5 text-[16px] font-medium text-white transition-colors hover:bg-[#D24A6E] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#13302F]"
          >
            {{ copied ? 'Copied to clipboard' : 'Copy the install command' }}
          </button>
          <a
            href="/docs"
            class="rounded-xl border border-[#C9DCD8] bg-white px-6 py-3.5 text-[16px] font-medium text-[#13302F] transition-colors hover:border-[#8FAEA9] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#E45C7F]"
          >
            Browse the docs
          </a>
        </div>
      </div>
    </section>

    <footer class="border-t border-[#D7E4E1]">
      <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 py-8 text-[14px] text-[#5C7C78] sm:flex-row">
        <p>Axolotl is free software, released under the MIT licence.</p>
        <p>Laravel {{ laravelVersion }} · PHP {{ phpVersion }}</p>
      </div>
    </footer>
  </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600&family=Public+Sans:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap');

.gill {
  transform-box: view-box;
  transform-origin: 210px 150px;
  animation: axolotl-sway 7s ease-in-out infinite;
}
.gill--right {
  animation-delay: -3.5s;
}

@keyframes axolotl-sway {
  0%, 100% { transform: rotate(-2.2deg); }
  50% { transform: rotate(2.2deg); }
}

@media (prefers-reduced-motion: reduce) {
  .gill { animation: none; }
}
</style>
