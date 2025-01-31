<template>
    <div class="min-h-screen">
        <MenuHeader />
        <div class="p-20 bg-gray-100 max-w-full max-h-full h-screen w-screen">
            <div class="flex items-center mb-8 l">
                <h1 class="text-2xl font-bold">Gerenciamento de Empréstimos</h1>
            </div>
            <div class="flex justify-start mb-4 space-x-4">
                <button @click="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Cadastrar
                    Empréstimo</button>
                <button @click="showAvailableCopiesModal = true" class="bg-green-500 text-white px-4 py-2 rounded">
                    Ver Cópias Disponíveis
                </button>
            </div>
            <AvailableCopiesModal :show="showAvailableCopiesModal" :groupedCopies="groupedAvailableCopies"
                @close="showAvailableCopiesModal = false" />
            <div class="overflow-x-auto sm:-mx-1 lg:-mx-9">
                <div class="py-2  align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-l-8 border-r-4 border-t-4 border-b-4 sm:rounded-lg">
                        <DataTable :data="loans" :columns="columns" class="display w-full" ref="dataTable" />
                    </div>
                </div>
            </div>

            <div v-if="showModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-bold mb-4">{{ editMode ? 'Editar Empréstimo' : 'Cadastrar Empréstimo' }}
                    </h2>
                    <form @submit.prevent="editMode ? updateLoan() : saveLoan()">
                        <div class="mb-4">
                            <label for="user" class="block text-sm font-medium">Escolha um usuário</label>
                            <v-select v-model="form.user_id" :options="localUsers" label="name"
                                :reduce="user => user.id" placeholder="Selecione um usuário" required
                                class="v-select rounded bg-white border-gray-300 border-none">
                            </v-select>
                        </div>
                        <div class="mb-4">
                            <label for="books" class="block text-sm font-medium">Selecione o(s) Livro(s)</label>
                            <multiselect v-model="form.copy_ids" :options="availableCopies" :multiple="true"
                                label="full_name" track-by="id" :close-on-select="false"
                                placeholder="Selecione as cópias" required>
                                <template slot="option" slot-scope="{ option }">
                                    {{ option.registration_number }} - {{ option.book.name }}
                                </template>
                                <template slot="tag" slot-scope="{ option }">
                                    <span class="multiselect__tag">
                                        {{ option.book.name }} ({{ option.registration_number }})
                                    </span>
                                </template>
                            </multiselect>
                        </div>
                        <div class="mb-4">
                            <label for="loan_date" class="block text-sm font-medium">Data de Empréstimo</label>
                            <input id="loan_date" v-model="form.loan_date" type="date" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="return_date" class="block text-sm font-medium">Data de Devolução</label>
                            <input id="return_date" v-model="form.return_date" type="date"
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="observation" class="block text-sm font-medium">Observação</label>
                            <textarea id="observation" v-model="form.observation"
                                class="border rounded w-full px-3 py-2"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" @click="closeModal"
                                class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-dt';
import vSelect from "vue-select";
import Multiselect from 'vue-multiselect';
import AvailableCopiesModal from './AvailableCopiesModal.vue';
import MenuHeader from './MenuHeader.vue';

export default {
    components: {
        vSelect,
        Multiselect,
        AvailableCopiesModal,
        MenuHeader
    },
    props: {
        loans: {
            type: Array,
            required: true,
        },
        // users: {
        //     type: Array,
        //     required: true,
        // },
        books: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            showModal: false,
            editMode: false,
            localUsers: [],
            availableCopies: [],
            showAvailableCopiesModal: false,
            groupedAvailableCopies: {},
            form: {
                id: null,
                user_id: null,
                book_ids: [],
                loan_date: '',
                return_date: '',
                observation: '',
                status: 'emprestimo',
            },
            columns: [
                {
                    title: 'Ações',
                    data: null,
                    render: (data, type, row) => {
                        return `
                            <div class="flex space-x-2">
                                <button onclick="window.editLoan(${row.id})" class="bg-blue-500 text-white px-2 py-1 rounded">Editar</button>
                                <button onclick="window.returnLoan(${row.id})" class="bg-green-500 text-white px-2 py-1 rounded">Devolvido</button>
                            </div>
                        `;
                    },
                },
                { title: 'Usuário', data: 'user.name' },
                {
                    title: 'Livros',
                    data: 'copies',
                    render: function (data) {
                        const grouped = {};
                        data.forEach(copy => {
                            const bookName = copy.book.name;
                            grouped[bookName] = (grouped[bookName] || 0) + 1;
                        });
                        return Object.entries(grouped).map(([name, count]) =>
                            `${name} (${count}) Cópias`
                        ).join(', ');
                    }
                },
                {
                    title: 'Data de Empréstimo',
                    data: 'loan_date',
                    render: (data) => data ? data.split('-').reverse().join('/') : '---'
                },
                {
                    title: 'Data de Devolução',
                    data: 'return_date',
                    render: (data, type, row) => {
                        const dateFormatted = data ? data.split('-').reverse().join('/') : '---';
                        const isOverdue = row.status === 'Empréstimo/Atrasado';

                        return `<span class="${isOverdue ? 'bg-red-500 text-white p-1 rounded' : ''}">${dateFormatted}</span>`;
                    }
                },
                { title: 'Observação', data: 'observation' },
                {
                    title: 'Status',
                    data: 'status',
                    render: (data) => {
                        const isOverdue = data === 'Empréstimo/Atrasado';
                        return `<span class="${isOverdue ? 'bg-red-500 text-white p-1 rounded' : ''}">${data}</span>`;
                    }
                },
            ],
        };
    },
    methods: {
        async loadAvailableCopies() {
            const response = await axios.get('/available-copies');
            const copies = response.data;

            this.groupedAvailableCopies = copies.reduce((acc, copy) => {
                const bookName = copy.book.name;
                acc[bookName] = (acc[bookName] || 0) + 1;
                return acc;
            }, {});
        },
        handleBookSelection(selectedBooks) {
            this.form.book_ids = selectedBooks.map(book => book.id);
        },

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
            this.form = { id: null, user_id: null, book_ids: [], loan_date: '', return_date: '', observation: '', status: 'emprestimo' };
        },
        saveLoan() {
            const payload = {
                ...this.form,
                copy_ids: this.form.copy_ids.map(copy => copy.id)
            };
            axios.post('/loans', payload)
                .then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Empréstimo cadastrado com sucesso!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    this.closeModal();
                    this.fetchLoans();
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                })
                .catch(() => {
                    Swal.fire('Erro!', 'Não foi possível cadastrar o empréstimo.', 'error');
                });
        },

        updateLoan() {
            const copyIds = this.form.copy_ids.map(copy => copy.id);
            const payload = {
                ...this.form,
                copy_ids: copyIds
            };
            axios.put(`/loans/${this.form.id}`, payload)
                .then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Empréstimo atualizado com sucesso!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    this.closeModal();
                    this.fetchLoans();
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                })
                .catch(() => {
                    Swal.fire('Erro!', 'Não foi possível atualizar o empréstimo.', 'error');
                });
        },
        returnLoan(id) {
            axios.get(`/loans/${id}`)
                .then((response) => {
                    const loan = response.data;
                    const userName = loan.user.name;
                    const groupedBooks = {};
                    loan.copies.forEach(copy => {
                        const bookName = copy.book.name;
                        groupedBooks[bookName] = (groupedBooks[bookName] || 0) + 1;
                    });
                    const booksList = Object.entries(groupedBooks)
                        .map(([name, count]) => `${name} (${count})`)
                        .join(', ');

                    Swal.fire({
                        title: 'Confirmar Exclusão',
                        html: `
                    <p>Deseja realmente marcar o empréstimo do usuário <strong>${userName}</strong> com os livros:</p>
                    <p><em>${booksList}</em> <strong>como DEVOLVIDO?</strong></p>
                    <p class="text-sm text-gray-500">Ao fazer isso, os livros voltarão para o estoque para novos empréstimos.</p>
                `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#10B981',
                        cancelButtonColor: '#EF4444',
                        confirmButtonText: 'Confirmar Exclusão',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`/loans/${id}`)
                                .then(() => {
                                    Swal.fire({
                                        title: 'Sucesso!',
                                        text: 'Empréstimo excluído e cópias liberadas!',
                                        icon: 'success',
                                        timer: 3000,
                                        showConfirmButton: false,
                                    });
                                    this.fetchLoans();
                                    setTimeout(() => {
                                    window.location.reload();
                                }, 3000);
                                })
                                .catch(() => {
                                    Swal.fire('Erro!', 'Falha ao excluir o empréstimo.', 'error');
                                });
                        }
                    });
                })
                .catch(() => {
                    Swal.fire('Erro!', 'Não foi possível carregar o empréstimo.', 'error');
                });
        },
        fetchUsers() {
            axios.get('/users-library/all')
                .then((response) => {
                    this.localUsers = response.data;
                })
                .catch((error) => {
                    console.error('Erro:', error);
                });
        },
        fetchLoans() {
            axios.get('/loans').then((response) => {
                this.$emit('update:loans', response.data);
            });
        },
        async fetchAvailableCopies() {
            const response = await axios.get('/available-copies');
            this.availableCopies = response.data.map(copy => ({
                ...copy,
                full_name: `${copy.registration_number} ${copy.book.name}`
            }));
        },
        editLoan(id) {
            axios.get(`/loans/${id}`)
                .then((response) => {
                    const selectedCopies = response.data.copies.map(copy => ({
                        ...copy,
                        full_name: `${copy.registration_number} - ${copy.book.name}`
                    }));
                    this.form = {
                        ...response.data,
                        copy_ids: selectedCopies
                    };
                    this.editMode = true;
                    this.showModal = true;
                })
                .catch(() => {
                    Swal.fire('Erro!', 'Não foi possível carregar os dados do empréstimo.', 'error');
                });
        },
    },
    mounted() {
        this.fetchUsers();
        this.fetchLoans();
        this.fetchAvailableCopies();
        this.loadAvailableCopies();
        window.editLoan = this.editLoan;
        window.returnLoan = this.returnLoan;
    },

};
</script>
