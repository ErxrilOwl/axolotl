<template>
  <aside
    :class="[
      'fixed flex flex-col mt-0 top-0 px-5 start-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-e border-gray-200',
      {
        'xl:w-[290px]': isExpanded || isMobileOpen || isHovered,
        'xl:w-[90px]': !isExpanded && !isHovered,
        'translate-x-0 w-[290px]': isMobileOpen,
        'max-xl:-translate-x-full max-xl:rtl:translate-x-full': !isMobileOpen,
        'xl:translate-x-0': true,
      },
    ]"
    @mouseenter="!isExpanded && (isHovered = true)"
    @mouseleave="isHovered = false"
  >
    <div
      :class="[
        'pt-8 pb-7 flex',
        !isExpanded && !isHovered ? 'xl:justify-center' : 'justify-start',
      ]"
    >
      <Link class="flex items-center gap-2.5" href="/dashboard">
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="dark:hidden"
          :src="Logo"
          alt="Logo"
          width="40"
          height="40"
        />
        <img
          v-if="isExpanded || isHovered || isMobileOpen"
          class="hidden dark:block"
          :src="Logo"
          alt="Logo"
          width="40"
          height="40"
        />
        <img
          v-else
          :src="Logo"
          alt="Logo"
          width="32"
          height="32"
        />
        Axolotl
      </Link>
    </div>
    <div
      class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar"
    >
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
            <h2
              :class="[
                'mb-4 text-xs uppercase flex leading-5 text-gray-400',
                !isExpanded && !isHovered
                  ? 'xl:justify-center'
                  : 'justify-start',
              ]"
            >
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-1">
              <li v-for="(item, index) in menuGroup.items" :key="item.name">
                <button
                  v-if="item.subItems"
                  @click="toggleSubmenu(groupIndex, index)"
                  :class="[
                    'menu-item group w-full',
                    {
                      'menu-item-active': isSubmenuOpen(groupIndex, index),
                      'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
                    },
                    !isExpanded && !isHovered
                      ? 'xl:justify-center'
                      : 'xl:justify-start',
                  ]"
                >
                  <span
                    :class="[
                      isSubmenuOpen(groupIndex, index)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text truncate"
                    >{{ item.name }}</span
                  >
                  <ChevronDownIcon
                    v-if="isExpanded || isHovered || isMobileOpen"
                    :class="[
                      'ms-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isSubmenuOpen(
                          groupIndex,
                          index
                        ),
                      },
                    ]"
                  />
                </button>
                <Link
                  v-else-if="item.path"
                  :href="item.path"
                  :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(item.path),
                      'menu-item-inactive': !isActive(item.path),
                    },
                  ]"
                >
                  <span
                    :class="[
                      isActive(item.path)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]"
                  >
                    <component :is="item.icon" />
                  </span>
                  <span
                    v-if="isExpanded || isHovered || isMobileOpen"
                    class="menu-item-text"
                    >{{ item.name }}</span
                  >
                </Link>
                <transition
                  @enter="startTransition"
                  @after-enter="endTransition"
                  @before-leave="startTransition"
                  @after-leave="endTransition"
                >
                  <div
                    v-show="
                      isSubmenuOpen(groupIndex, index) &&
                      (isExpanded || isHovered || isMobileOpen)
                    "
                  >
                    <ul class="mt-2 space-y-1 ms-9">
                      <li v-for="subItem in item.subItems" :key="subItem.name">
                        <Link
                          :href="subItem.path"
                          :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive(
                                subItem.path
                              ),
                              'menu-dropdown-item-inactive': !isActive(
                                subItem.path
                              ),
                            },
                          ]"
                        >
                          {{ subItem.name }}
                          <span class="flex items-center gap-1 ms-auto">
                            <span
                              v-if="subItem.new"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              new
                            </span>
                            <span
                              v-if="subItem.pro"
                              :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path
                                  ),
                                },
                              ]"
                            >
                              pro
                            </span>
                          </span>
                        </Link>
                      </li>
                    </ul>
                  </div>
                </transition>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup lang="ts">
import Logo from '@/assets/logo.svg';
import { watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import { useSidebar } from '@/composables/useSidebar'
import {
  CalenderIcon,
  ChevronDownIcon,
  GridIcon,
  HorizontalDots,
  ListIcon,
  PageIcon,
  PieChartIcon,
  PlugInIcon,
  SettingsIcon,
  TableIcon,
  UserCircleIcon,
  BoxCubeIcon
} from '@/icons';
import SidebarWidget from './SidebarWidget.vue'

const page = usePage()

const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar()

interface SubItem {
  name: string
  path: string
  pro?: boolean
  new?: boolean
}

interface MenuItem {
  icon?: any
  name: string
  path?: string
  subItems?: SubItem[]
  new?: boolean
  pro?: boolean
}

interface MenuGroup {
  title: string
  items: MenuItem[]
}

const menuGroups: MenuGroup[] = [
  {
    title: 'Menu',
    items: [
      {
        icon: SettingsIcon,
        name: 'Administration',
        subItems: [
          { name: 'Users', path: '/users' },
          { name: 'Departments', path: '/departments' },
          { name: 'Roles', path: '/roles' },
          { name: 'Permissions', path: '/permissions' },
        ],
      },
    //   {
    //     name: 'Pages',
    //     icon: PageIcon,
    //     subItems: [
    //       { name: 'Blank Page', path: '/blank', pro: false },
    //       { name: '404 Page', path: '/error-404', pro: false },
    //     ],
    //   },
    ],
  },
]

const currentPath = () => page.url.split('?')[0]
const isActive = (path?: string) => (path ? currentPath() === path : false)

const setActiveMenuFromRoute = () => {
  menuGroups.forEach((group, groupIndex) => {
    group.items.forEach((item, itemIndex) => {
      if (item.subItems?.some((subItem) => isActive(subItem.path))) {
        openSubmenu.value = `${groupIndex}-${itemIndex}`
      }
    })
  })
}

watch(
  () => page.url,
  () => {
    setActiveMenuFromRoute()
  },
  { immediate: true },
)

const toggleSubmenu = (groupIndex: number, itemIndex: number) => {
  const key = `${groupIndex}-${itemIndex}`
  openSubmenu.value = openSubmenu.value === key ? null : key
}

const isSubmenuOpen = (groupIndex: number, itemIndex: number) => {
  const key = `${groupIndex}-${itemIndex}`
  return openSubmenu.value === key
}

const startTransition = (el: Element) => {
  const htmlEl = el as HTMLElement
  htmlEl.style.height = 'auto'
  const height = htmlEl.scrollHeight
  htmlEl.style.height = '0px'
  void htmlEl.offsetHeight // force reflow
  htmlEl.style.height = height + 'px'
}

const endTransition = (el: Element) => {
  const htmlEl = el as HTMLElement
  htmlEl.style.height = ''
}
</script>
