<template>
    <div class="min-h-screen">
        <MenuHeader />
        <div class="p-8 bg-gray-100 max-w-full max-h-full h-screen w-screen">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Gerenciamento de Livros</h1>

            </div>
            <div class="flex justify-start mb-4">
                <button @click="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Cadastrar Livro</button>
            </div>
            <div class="overflow-x-auto sm:-mx-1 lg:-mx-5">
                <div class="py-8 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-l-8 border-r-4 border-t-4 border-b-4 sm:rounded-lg">
                        <DataTable :data="books" :columns="columns" class="display w-full" ref="dataTable" />
                    </div>
                </div>
            </div>
            <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
                <div class="bg-white rounded shadow p-9 w-full max-w-4xl overflow-auto text-black">
                    <h2 class="text-xl font-bold mb-4">{{ editMode ? 'Editar Livro' : 'Cadastrar Livro' }}</h2>
                    <form @submit.prevent="editMode ? updateBook() : saveBook()">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium">Nome</label>
                            <input id="name" v-model="form.name" type="text" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="author" class="block text-sm font-medium">Autor</label>
                            <input id="author" v-model="form.author" type="text" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium">Descrição</label>
                            <textarea id="description" v-model="form.description" required
                                class="border rounded w-full px-3 py-2"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium">Imagem (PNG)</label>
                            <input id="image" type="file" @change="handleImageUpload" accept="image/png" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <!-- <div class="mb-4">
                      <label for="status" class="block text-sm font-medium">Status</label>
                      <select id="status" v-model="form.status" required
                          class="border rounded w-full px-3 py-2">
                          <option value="">Selecione o status</option>
                          <option value="Encomendado">Encomendado</option>
                          <option value="Disponível">Disponível</option>
                      </select>
                  </div> -->
                        <div class="mb-4">
                            <label for="genres" class="block text-sm font-medium">Gêneros</label>
                            <multiselect v-model="form.genres" :options="genres" :multiple="true" :taggable="true"
                                tag-placeholder="Adicionar como nova tag" tag-position="bottom"
                                placeholder="Selecione os gêneros"></multiselect>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="closeModal"
                                class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="p-6">
                <manage-copies-modal v-if="showCopiesModal" :book-id="selectedBook?.id"
                    :book-title="selectedBook?.title" :copies="selectedBook?.copies" :show="showCopiesModal"
                    @close="showCopiesModal = false" />
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-dt';
import Multiselect from 'vue-multiselect';
import ManageCopiesModal from './ManageCopiesModal.vue';
import MenuHeader from './MenuHeader.vue';

export default {
    components: {
        Multiselect,
        ManageCopiesModal,
        MenuHeader
    },
    props: {
        books: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            showModal: false,
            editMode: false,
            showCopiesModal: false,
            selectedBook: null,
            form: {
                id: null,
                name: '',
                author: '',
                description: '',
                image: null,
                genres: [],
            },
            genres: ['Ficção', 'Romance', 'Fantasia', 'Aventura', 'Terror', 'Ação', 'Drama', 'Suspense', 'Anime', 'Historia', 'Biografia', 'Poesia', 'AutoBiografia', 'Outros'],
            table: null,
            columns: [
                {
                    title: 'Ações',
                    data: null,
                    render: (data, type, row) => {
                        return `
                         <div class="relative inline-block text-center">
                  <button type="button"
                    class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-blue-700 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    id="dropdownButton-${row.id}"
                    aria-expanded="true"
                    aria-haspopup="true"
                    onclick="toggleDropdown(${row.id})">
                    Ações
                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                  </button>

                  <div id="dropdown-${row.id}"
                    class="dropdown-menu hidden z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-md w-38 fixed right-90 mt-2 origin-top-right">
                    <ul class="py-2 text-sm text-gray-700">
                        <li>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="window.editBook(${row.id})">Editar</a>
                        </li>
                        <li>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="window.manageCopies(${row.id})">Gerenciar Cópias</a>
                        </li>
                        <li>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="window.deleteBook(${row.id}, '${row.name}')">Excluir</a>
                        </li>
                    </ul>
                  </div>
                </div>`;
                    }
                },
                {
                    title: 'Cópias',
                    data: 'available_copies',
                    render: (data) => data > 0 ? `${data} disponíveis` : 'Indisponível'
                },

                { title: 'Nome', data: 'name' },
                { title: 'Autor', data: 'author' },
                { title: 'Descrição', data: 'description' },
                { title: 'Gêneros', data: 'genres', render: (data) => data.join(', ') },
            ],
        };
    },
    methods: {
        openModal() {
            this.showModal = true;
            this.editMode = false;
            this.clearForm();
        },
        closeModal() {
            this.showModal = false;
            this.clearForm();
        },
        clearForm() {
            this.form = {
                id: null,
                name: '',
                author: '',
                description: '',
                image: null,
                genres: []
            };
        },
        handleImageUpload(event) {
            this.form.image = event.target.files[0];
        },
        async saveBook() {
            try {
                const formData = new FormData();
                formData.append('name', this.form.name);
                formData.append('author', this.form.author);
                formData.append('description', this.form.description);
                formData.append('genres', JSON.stringify(this.form.genres));

                if (this.form.image) {
                    formData.append('image', this.form.image);
                }

                await axios.post('/books', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Livro cadastrado com sucesso!',
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false,
                });
                this.closeModal();
                this.fetchBooks();
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            } catch (error) {
                Swal.fire('Erro!', 'Falha ao cadastrar livro', 'error');
            }
        },
        updateBook() {
            const formData = new FormData();
            formData.append('name', this.form.name);
            formData.append('author', this.form.author);
            formData.append('description', this.form.description);

            this.form.genres.forEach(genre => {
                formData.append('genres[]', genre);
            });

            if (this.form.image instanceof File) {
                formData.append('image', this.form.image);
            }

            formData.append('_method', 'PUT');

            axios.post(`/books/${this.form.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Livro atualizado com sucesso!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    this.closeModal();
                    this.fetchBooks();
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                })
                .catch((error) => {
                    Swal.fire('Erro!', error.response?.data?.message || 'Erro na atualização', 'error');
                });
        },

        deleteBook(id, bookName) {
            Swal.fire({
                title: `Deseja realmente deletar o livro ${bookName}?`,
                text: "Essa ação não pode ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/books/${id}`).then(() => {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Livro deletado com sucesso!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        this.closeModal();
                        this.fetchBooks();
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }).catch(() => {
                        Swal.fire('Erro!', 'Não foi possível deletar o livro.', 'error');
                    });
                }
            });
        },
        fetchBooks() {
            axios.get('/books')
                .then((response) => {
                    this.$emit('update:books', response.data);
                    this.refreshDataTable();

                    if (this.table) {
                        this.table.destroy();
                        this.$nextTick(() => {
                            this.table = new DataTable(this.$refs.dataTable, {

                            });
                        });
                    }
                });
        },
        editBook(id) {
            axios.get(`/books/${id}`).then((response) => {
                this.form = {
                    id: response.data.id,
                    name: response.data.name,
                    author: response.data.author,
                    description: response.data.description,
                    image: response.data.image,
                    genres: response.data.genres
                };
                this.editMode = true;
                this.showModal = true;
            }).catch(() => {
                Swal.fire('Erro!', 'Não foi possível carregar os dados do livro.', 'error');
            });
        },
        manageCopies(bookId) {
            const book = this.books.find(b => b.id === bookId);
            this.selectedBook = {
                id: bookId,
                title: book.name,
                copies: book.copies,
                availableCopies: book.available_copies
            };
            this.showCopiesModal = true;
        },


        refreshDataTable() {
            if (this.table) {
                this.table.destroy();
            }
        },
    },
    mounted() {
        this.refreshDataTable();
        window.editBook = this.editBook;
        window.deleteBook = this.deleteBook;
        window.manageCopies = this.manageCopies;

        window.toggleDropdown = (id) => {
            const dropdown = document.getElementById(`dropdown-${id}`);
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }
            document.querySelectorAll('.dropdown-menu').forEach((menu) => {
                if (menu.id !== `dropdown-${id}`) {
                    menu.classList.add('hidden');
                }
            });
        };

        document.addEventListener('click', (event) => {
            const isDropdownButton = event.target.closest('[id^="dropdownButton-"]');
            if (!isDropdownButton) {
                document.querySelectorAll('.dropdown-menu').forEach((menu) => {
                    menu.classList.add('hidden');
                });
            }
        });
    },
};
</script>
