<template>
    <nav class="bg-white shadow-lg fixed w-full z-50">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <Link
              href="/"
              class="text-xl font-bold text-gray-800 hover:text-blue-600 transition-colors"
            >
              Minha Biblioteca
            </Link>
          </div>

          <div class="hidden md:flex space-x-8 relative z-50">
            <Link
              href="/users-library"
              class="nav-link cursor-pointer"
              :class="{ 'border-b-2 border-blue-500 text-blue-600': $page.url === '/users-library' }"
            >
              Usuários
            </Link>


            <Link
              href="/books"
              class="nav-link cursor-pointer"
              :class="{ 'border-b-2 border-blue-500 text-blue-600': $page.url.startsWith('/books') }"
            >
              Livros
            </Link>

            <Link
              href="/loans"
              class="nav-link cursor-pointer"
              :class="{ 'border-b-2 border-blue-500 text-blue-600': $page.url.startsWith('/loans') }"
            >
              Empréstimos
            </Link>
            <Link
                href="/logout"
                method="post"
                class="nav-link"
                >
                Sair
            </Link>
          </div>
        </div>
      </div>
    </nav>
  </template>

  <script>
  import { Link } from '@inertiajs/vue3';

  export default {
    components: {
      Link
    }
  }
  </script>

  <style scoped>
  .nav-link {
    @apply px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors font-medium;
    display: inline-block;
    position: relative;
  }
  </style>
