<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import CreateModal from './Create.vue';
    import EditModal from './Edit.vue';
    import { Head } from '@inertiajs/vue3';
    import { Table } from 'ant-design-vue';
    import { ref, computed, inject } from 'vue';
    import { usePagination } from 'vue-request';
    import axios from 'axios';

    const info = inject("info");
    const handleModalClose = () => {
        editModalVisible.value = false;
    };

    const columns = [
    {
        title: 'Id',
        dataIndex: 'id',
    },
    {
        title: 'Name',
        dataIndex: 'name',
    },
    {
        title: 'Email',
        dataIndex: 'email',
    },
    {
        title: 'Logo',
        key: 'logo',
    },
    {
        title: 'Website',
        key: 'website',
    },
    {
        title: 'Action',
        key: 'action',
    },
    ];

const dataSource = ref([]);
const loading = ref(true);
const pagination = ref({
  total: 0,
  current: 1,
  pageSize: 10,
});

const fetchData = async (params = {}) => {
    
  loading.value = true;
  try {
    const response = await axios.get('/companies', { params });
    dataSource.value = response.data.data;
    pagination.value.total = response.data.total;
    pagination.value.current = response.data.current_page;
    pagination.value.pageSize = response.data.per_page;
  } catch (error) {
    console.error('Error fetching data:', error);
  } finally {
    loading.value = false;
  }
};

const handleTableChange = (pag, filters, sorter) => {
  fetchData({
    page: pag.current,
    results: pag.pageSize,
    sortField: sorter.field,
    sortOrder: sorter.order,
    ...filters,
  });
};

fetchData();


  // State for modal
  const editModalVisible = ref(false);
  const selectedCompany = ref(null);

  // Method to open the edit modal
  const openEditModal = (company) => {
    selectedCompany.value = company
    editModalVisible.value = true;
  };

  const formattedWebsite = (website) => {
  if (!website.startsWith('http://') && !website.startsWith('https://')) {
    return 'http://' + website;
  }
  return website;
};

const onDelete = key => {
  axios.delete('/companies/'+key)
            .then(response => {
               fetchData()
               info('success',response.data.message);
            })
            .catch(error => {
                console.error('Error submitting form:', error);
            });
          };

</script>
    
    <template>
        <Head title="Companies" />
    
        <AuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Companies</h2>
                <CreateModal @companyCreated="fetchData"/>
                <EditModal :editModalVisible="editModalVisible" :company="selectedCompany" @handleModalClose="handleModalClose" @companyCreated="fetchData" />
            </template>

            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <a-table
                            :columns="columns"
                            :row-key="record => record.id"
                            :data-source="dataSource"
                            :pagination="pagination"
                            :loading="loading"
                            @change="handleTableChange"
                        >
                        <template #bodyCell="{ column, record }">
                            <template v-if="column.key === 'action'">
                                <a @click="openEditModal(record)" class="mr-2 text-blue-500">Edit</a>
                                <a-popconfirm
                                v-if="dataSource.length"
                                title="Are you Sure to delete?"
                                @confirm="onDelete(record.id)"
                              >
                                <a class="text-rose-700	">Delete</a>
                              </a-popconfirm>
                            </template>
                            <template v-if="column.key === 'website'">
                                <a :href="formattedWebsite(record.website)" target="_blank" class="text-blue-500">{{record.website}}</a>
                            </template>
                            <template v-if="column.key === 'logo'">
                                <a-image
                                    :width="50"
                                    :src="`/storage/${record.logo}`"
                                />                         
                            </template>
                         
                        </template>
                        </a-table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
    