<template>
    <!-- Div do fundo escuro com z-index alto -->
    <div v-if="show" class="fixed inset-0 z-50">
      <!-- Overlay escuro que cobre toda a tela -->
      <div
        class="absolute inset-0 bg-black bg-opacity-50"
        @click="close" 
      ></div>

      <!-- Conteúdo do modal com z-index mais alto -->
      <div class="relative z-50 flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl mx-4">
          <h2 class="text-xl font-bold mb-4">Cópias Disponíveis para Empréstimo</h2>

          <div class="max-h-96 overflow-y-auto">
            <div
              v-for="(count, bookName) in groupedCopies"
              :key="bookName"
              class="flex justify-between items-center p-2 border-b"
            >
              <span class="font-medium">{{ bookName }}</span>
              <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                {{ count }} disponível{{ count > 1 ? 's' : '' }}
              </span>
            </div>
          </div>

          <button
            @click="close"
            class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors"
          >
            Fechar
          </button>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      show: Boolean,
      groupedCopies: Object
    },
    methods: {
      close() {
        this.$emit('close');
      }
    }
  };
  </script>
