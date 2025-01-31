<template>
    <div class="min-h-screen">
        <MenuHeader />
        <div class="p-20 bg-gray-100 max-w-full max-h-full h-screen w-screen">
            <div class="flex justify-between items-center mb-10">
                <h1 class="text-2xl font-bold">Gerenciamento de Usuários</h1>
            </div>
            <div class="flex justify-start mb-4 space-x-4">
                <button @click="openModal()" class="bg-blue-500 text-white px-4 py-2 rounded">Cadastrar Usuário</button>
            </div>
            <div class="overflow-x-auto sm:-mx-1 lg:-mx-5">
                <div class="py-8 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <DataTable :data="users" :columns="columns" class="display w-full" ref="dataTable" />
                    </div>
                </div>
            </div>

            <div v-if="showModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-xl font-bold mb-4">{{ editMode ? 'Editar Usuário' : 'Cadastrar Usuário' }}</h2>
                    <form @submit.prevent="editMode ? updateUser() : saveUser()">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium">Nome</label>
                            <input id="name" v-model="form.name" type="text" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="cpf" class="block text-sm font-medium">CPF</label>
                            <input id="cpf" v-model="form.cpf" type="text" v-mask="'###.###.###-##'"
                                placeholder="000.000.000-00" required class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input id="email" v-model="form.email" type="email" required
                                class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="contact" class="block text-sm font-medium">Contato</label>
                            <input id="contact" v-model="form.contact" type="text" v-mask="'(##) #####-####'"
                                placeholder="(00) 00000-0000" required class="border rounded w-full px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium">Endereço</label>
                            <input id="address" v-model="form.address" type="text" required
                                class="border rounded w-full px-3 py-2">
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
import MenuHeader from './MenuHeader.vue';

export default {
    components: {
        MenuHeader
    },
    props: {
        users: {
            type: Array,
            required: true,
            total: Number,
            currentPage: Number,
            lastPage: Number
        },
    },
    data() {
        return {
            showModal: false,
            editMode: false,
            form: {
                id: null,
                name: '',
                cpf: '',
                email: '',
                contact: '',
                address: '',
            },
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
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="window.editUser(${row.id})">Editar</a>
                        </li>
                        <li>
                        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="window.deleteUser(${row.id}, '${row.name}')">Excluir</a>
                        </li>
                    </ul>
                  </div>
                </div>
              `;
                    },
                },
                { title: 'Nome', data: 'name' },
                { title: 'CPF', data: 'cpf' },
                { title: 'Email', data: 'email' },
                { title: 'Contato', data: 'contact' },
                { title: 'Endereço', data: 'address' },

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
            this.form = { id: null, name: '', cpf: '', email: '', contact: '', address: '' };
        },
        saveUser() {
            const sanitizedData = {
                ...this.form,
                cpf: this.form.cpf.replace(/\D/g, ''),
                contact: this.form.contact.replace(/\D/g, '')
            };

            axios.post('/users-library', sanitizedData)
                .then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Usuário cadastrado com sucesso!',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    this.closeModal();
                    this.fetchUsers();
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        let errorMessages = [];

                        if (errors.email) {
                            errorMessages.push('O e-mail informado já está cadastrado.');
                        }
                        if (errors.cpf) {
                            errorMessages.push('O CPF informado já está cadastrado.');
                        }

                        Swal.fire({
                            title: 'Erro!',
                            text: errorMessages.join('\n'),
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    } else {
                        Swal.fire('Erro!', 'Não foi possível cadastrar o usuário.', 'error');
                    }
                });
        },

        updateUser() {
            const sanitizedData = {
                ...this.form,
                cpf: this.form.cpf.replace(/\D/g, ''),
                contact: this.form.contact.replace(/\D/g, '')
            };

            axios.put(`/users-library/${this.form.id}`, sanitizedData)
                .then(() => {
                    Swal.fire({
                        title: 'Sucesso!',
                        text: 'Usuário atualizado com sucesso!',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    this.closeModal();
                    this.fetchUsers();
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                })
                .catch((error) => {
                    if (error.response && error.response.status === 422) {
                        const errors = error.response.data.errors;
                        let errorMessages = [];

                        if (errors.email) {
                            errorMessages.push('O e-mail informado já está cadastrado.');
                        }
                        if (errors.cpf) {
                            errorMessages.push('O CPF informado já está cadastrado.');
                        }

                        Swal.fire({
                            title: 'Erro!',
                            text: errorMessages.join('\n'),
                            icon: 'error',
                            confirmButtonText: 'OK',
                        });
                    } else {
                        Swal.fire('Erro!', 'Não foi possível atualizar o usuário.', 'error');
                    }
                });
        },

        deleteUser(id, userName) {
            Swal.fire({
                title: `Deseja realmente deletar esse usuário ${userName}?`,
                text: "Essa ação não pode ser desfeita.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/users-library/${id}`).then(() => {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Usuário deletado com sucesso!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000,
                        });
                        this.closeModal();
                        this.fetchUsers();
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }).catch(() => {
                        Swal.fire('Erro!', 'Não foi possível deletar o usuário.', 'error');
                    });
                }
            });
        },
        fetchUsers() {
            axios.get('/users-library').then((response) => {
                this.$emit('update:users', response.data);
                this.refreshDataTable();
            });
        },
        editUser(id) {
            axios.get(`/users-library/${id}`).then((response) => {
                this.form = response.data;
                this.editMode = true;
                this.showModal = true;
            }).catch(() => {
                Swal.fire('Erro!', 'Não foi possível carregar os dados do usuário.', 'error');
            });
        },
        refreshDataTable() {
            if (this.table) {
                this.table.destroy();
            }
        },
    },
    mounted() {
        this.refreshDataTable();
        window.editUser = this.editUser;
        window.deleteUser = this.deleteUser;

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
