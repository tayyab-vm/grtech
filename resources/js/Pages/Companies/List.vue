<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
    import CreateModal from './Create.vue';
    import EditModal from './Edit.vue';
    import { Head } from '@inertiajs/vue3';
    import { Table } from 'ant-design-vue';
    import { ref, computed } from 'vue';
    import { usePagination } from 'vue-request';
    import axios from 'axios';

    const createModalVisible = ref(false);

const openCreateModal = () => {
  createModalVisible.value = true;
};

const handleModalClose = () => {
  createModalVisible.value = false;
};

    const columns = [
  {
    title: 'Name',
    dataIndex: 'name',
  },
  {
    title: 'Email',
    dataIndex: 'email',
  },
  {
    title: 'Created At',
    dataIndex: 'created_at',
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
    const response = await axios.get('/users', { params });
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
  const selectedCompanyId = ref(null);

  // Method to open the edit modal
  const openEditModal = (companyId) => {
    // alert(companyId);
    selectedCompanyId.value = companyId;
    editModalVisible.value = true;
    alert(editModalVisible.value);
  };

</script>
    
    <template>
        <Head title="Companies" />
    
        <AuthenticatedLayout>
            <template #header>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
                <CreateModal />
                <EditModal :title="editModalVisible" @update:title="handleModalClose" />
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
                                <a @click="openEditModal(record.id)">Edit</a>
                            </template>
                        </template>
                        </a-table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
    