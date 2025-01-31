<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
        <div class="bg-white rounded shadow p-6 w-full max-w-4xl text-black">
            <h2 class="text-xl font-bold mb-4">Gerenciar Números de Registros das Cópias</h2>
            <h3 class="text-xl font-bold mb-4">Livro: {{ bookTitle }}</h3>

            <div class="mb-4">
                <label class="block text-sm font-medium">Adicionar Cópias (Separar por vírgula)</label>
                <textarea v-model="newCopies" class="border rounded w-full px-3 py-2 h-32"
                    placeholder="Ex: NUM-001, NUM-002, NUM-003"></textarea>
                <button @click="addCopies" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">
                    Adicionar Cópias
                </button>
            </div>
            <div v-if="formErrors.length" class="text-red-500">
                <ul>
                    <li v-for="(error, index) in formErrors" :key="index">{{ error }}</li>
                </ul>
            </div>

            <div class="mb-4">
                <h3 class="font-bold mb-2">Cópias Existentes ({{ copies.length }})</h3>
                <div class="border rounded p-4 max-h-64 overflow-y-auto">
                    <div v-for="copy in copies" :key="copy.id" class="flex items-center justify-between mb-2">
                        <span>{{ copy.registration_number }}</span>
                        <button @click="deleteCopy(copy.id)" class="text-red-500 hover:text-red-700">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
            <button @click="close" class="bg-gray-500 text-white px-4 py-2 rounded">Fechar</button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    props: ['bookId', 'bookTitle', 'show'],
    data() {
        return {
            newCopies: '',
            copies: [],
            formErrors: []
        };
    },
    mounted() {
        console.log('Componente montado');
        this.loadCopies();
    },
    methods: {
        async loadCopies() {
            console.log('Método loadCopies chamado nome de Jesus!');
            try {
                const response = await axios.get(`/books/${this.bookId}/copies`);
                console.log('Cópias carregadas:', response.data);
                this.copies = response.data;
            } catch (error) {
                console.error('Erro ao carregar as cópias:', error);
            }
        },

        async addCopies() {
            if (!this.newCopies.trim()) return;

            const numbers = this.newCopies.split(',')
                .map(n => n.trim())
                .filter(n => n.length > 0);

            try {
                await axios.post(`/books/${this.bookId}/copies`, { registration_numbers: numbers });
                this.newCopies = '';
                this.loadCopies();
                this.$emit('copies-updated');
                this.formErrors = [];
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.formErrors = Object.values(error.response.data.errors).flat();
                } else {
                    console.error('Erro ao adicionar as cópias:', error);
                    this.formErrors = ['Erro alienígena ao adicionar as cópias.'];
                }
            }
        },

        async deleteCopy(copyId) {
            await axios.delete(`/copies/${copyId}`);
            this.loadCopies();
            this.$emit('copies-updated');
        },

        close() {
            this.$emit('close');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    },
    watch: {
        show(val) {
            if (val) {
                this.loadCopies();
            }
        }
    }
};
</script>
