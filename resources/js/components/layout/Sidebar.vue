<template>
  <aside class="w-72 bg-white flex flex-col h-screen sticky top-0 border-r border-gray-200">
    <BrandingHeader />
    <Navigation />
    <UserProfile />
  </aside>
</template>

<script>
import BrandingHeader from './BrandingHeader.vue'
import Navigation from './Navigation.vue'
import UserProfile from './UserProfile.vue'

export default {
  name: 'Sidebar',
  components: {
    BrandingHeader,
    Navigation,
    UserProfile
  }
}
</script>