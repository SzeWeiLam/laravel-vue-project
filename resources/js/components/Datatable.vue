<template>
    <div class="page-style">
        <div class="card-body">
            <div v-if="success == true" class="alert alert-success" role="alert">
                {{ message }}
            </div>
            <div v-if="success == false" class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ message }}
            </div>
            <form @submit="formSubmit" enctype="multipart/form-data" class="row">
                <input type="file" class="form-control col-6 mr-3" v-on:change="onFileChange">
                <button class="btn btn-success col-2">Upload</button>
            </form>
        </div>
        <div style="margin-bottom: 20px;">
            <h2>Product Master List</h2>
        </div>
        <div class="table-style">
            <input class="input" type="text" v-model="search" placeholder="Search..." @input="resetPagination()"
                style="width: 250px;">
            <div class="control">
                <div class="select">
                    <select v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th v-for="column in columns" :key="column.name" @click="sortBy(column.name)"
                        :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                        style="width: 40%; cursor:pointer;">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in paginatedUsers" :key="user.id">
                    <td>{{ user.product_id }}</td>
                    <td>{{ user.types }}</td>
                    <td>{{ user.brand }}</td>
                    <td>{{ user.model }}</td>
                    <td>{{ user.capacity }}</td>
                    <td>{{ user.quantity }}</td>
                </tr>
            </tbody>
        </table>
        <div>
            <nav class="pagination" v-if="!tableShow.showdata">
                <span class="page-stats">{{ pagination.from }} - {{ pagination.to }} of {{ pagination.total }}</span>
                <a v-if="pagination.prevPageUrl" class="btn btn-sm btn-primary pagination-previous"
                    @click="--pagination.currentPage"> Prev </a>
                <a class="btn btn-sm btn-primary pagination-previous" v-else disabled> Prev </a>
                <a v-if="pagination.nextPageUrl" class="btn btn-sm pagination-next" @click="++pagination.currentPage"> Next
                </a>
                <a class="btn btn-sm btn-primary pagination-next" v-else disabled> Next </a>
            </nav>
            <nav class="pagination" v-else>
                <span class="page-stats">
                    {{ pagination.from }} - {{ pagination.to }} of {{ filteredProducts.length }}
                    <span v-if="`filteredProducts.length < pagination.total`"></span>
                </span>
                <a v-if="pagination.prevPage" class="btn btn-sm btn-primary pagination-previous"
                    @click="--pagination.currentPage"> Prev </a>
                <a class="btn btn-sm pagination-previous btn-primary" v-else disabled> Prev </a>
                <a v-if="pagination.nextPage" class="btn btn-sm btn-primary pagination-next"
                    @click="++pagination.currentPage"> Next </a>
                <a class="btn btn-sm pagination-next btn-primary" v-else disabled> Next </a>
            </nav>
        </div>
    </div>
</template>
<script>
export default {
    created() {
        this.getProducts();
    },
    data() {
        let sortOrders = {};
        let columns = [
            { label: 'Product ID', name: 'product_id' },
            { label: 'Types', name: 'types' },
            { label: 'Brand', name: 'brand' },
            { label: 'Model', name: 'model' },
            { label: 'Capacity', name: 'capacity' },
            { label: 'Quantity', name: 'quantity' },
        ];
        columns.forEach((column) => {
            sortOrders[column.name] = -1;
        });
        return {
            success: -1,
            message: '',
            users: [],
            columns: columns,
            sortKey: 'product_id',
            sortOrders: sortOrders,
            length: 10,
            search: '',
            tableShow: {
                showdata: true,
            },
            pagination: {
                currentPage: 1,
                total: '',
                nextPage: '',
                prevPage: '',
                from: '',
                to: ''
            },
        }
    },
    methods: {
        getProducts() {
            axios.get('/index/', { params: this.tableShow })
                .then(response => {
                    console.log('The data: ', response.data)
                    this.users = response.data;
                    this.pagination.total = this.users.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        paginate(array, length, pageNumber) {
            this.pagination.from = array.length ? ((pageNumber - 1) * length) + 1 : ' ';
            this.pagination.to = pageNumber * length > array.length ? array.length : pageNumber * length;
            this.pagination.prevPage = pageNumber > 1 ? pageNumber : '';
            this.pagination.nextPage = array.length > this.pagination.to ? pageNumber + 1 : '';
            return array.slice((pageNumber - 1) * length, pageNumber * length);
        },
        resetPagination() {
            this.pagination.currentPage = 1;
            this.pagination.prevPage = '';
            this.pagination.nextPage = '';
        },
        sortBy(key) {
            this.resetPagination();
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        onFileChange(e) {
            console.log(e.target.files[0]);
            this.file = e.target.files[0];
        },
        formSubmit(e) {
            console.log('submit');
            e.preventDefault();
            let currentObj = this;

            const config = {
                headers: { 'content-type': 'multipart/form-data' }
            }

            let formData = new FormData();
            formData.append('file', this.file);

            axios.post('/submit', formData, config)
                .then(function (response) {
                    currentObj.success = response.data.success;
                    currentObj.message = response.data.message;
                    if(currentObj.message){
                        currentObj.getProducts();
                    }
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
        }
    },
    computed: {
        filteredProducts() {
            let users = this.users;
            if (this.search) {
                users = users.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                    })
                });
            }
            let sortKey = this.sortKey;
            let order = this.sortOrders[sortKey] || 1;
            if (sortKey) {
                users = users.slice().sort((a, b) => {
                    let index = this.getIndex(this.columns, 'name', sortKey);
                    a = String(a[sortKey]).toLowerCase();
                    b = String(b[sortKey]).toLowerCase();
                    if (this.columns[index].type && this.columns[index].type === 'date') {
                        return (a === b ? 0 : new Date(a).getTime() > new Date(b).getTime() ? 1 : -1) * order;
                    } else if (this.columns[index].type && this.columns[index].type === 'number') {
                        return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
                    } else {
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    }
                });
            }
            return users;
        },
        paginatedUsers() {
            return this.paginate(this.filteredProducts, this.length, this.pagination.currentPage);
        }
    }
};
</script>
<style lang="scss">
.page-style {
    width: 70%;
    margin: auto;
    margin-top: 20px;

    .table-style {
        margin-bottom: 10px;

        input {
            width: 175px;
        }

        .control {
            float: right;
        }
    }

    .table {
        width: 100%;

        .sorting {
            background-repeat: no-repeat;
            background-position: center right;
        }

        .sorting_asc {
            background-repeat: no-repeat;
            background-position: center right;
        }

        .sorting_desc {
            background-repeat: no-repeat;
            background-position: center right;
        }
    }

    nav a {
        color: white !important;
        margin-right: 10px;
    }
}

h1 {
    text-align: center;
    font-size: 30px;
}

.pagination {
    justify-content: flex-end !important;

    .page-stats {
        align-items: center;
        margin-right: 5px;
    }

    i {
        color: #3273dc !important;
    }
}

</style>