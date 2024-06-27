<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { reactive, ref, toRaw, defineProps, watch, inject } from "vue";

const props = defineProps(["editModalVisible", "company"]);
const info = inject("info");
const emit = defineEmits(["handleModalClose", "companyCreated"]);

const formRef = ref();
const visible = ref(props.editModalVisible);
const company = ref(props.company);
const fileList = ref([]);

const formState = reactive({
    name: "",
    email: "",
    website: "",
    modifier: "public",
});

const beforeUpload = (file) => {
    formState.file = file;
    fileList.value = [file];
    return false; // Prevent automatic upload
};

const handleRemove = () => {
    formState.file = null;
    fileList.value = [];
};

watch(
    () => props.editModalVisible,
    (newValue) => {
        visible.value = newValue;
    }
);

watch(
    () => props.company,
    (newValue) => {
        formState.id = newValue.id;
        formState.name = newValue.name;
        formState.email = newValue.email;
        formState.website = newValue.website;
    }
);

const onOk = () => {
    formRef.value
        .validateFields()
        .then(() => {
            const formData = new FormData();
            formData.append("file", formState.file);
            formData.append("name", formState.name);
            formData.append("email", formState.email);
            formData.append("website", formState.website);
            formData.append("_method", "PUT");
            axios
                .post("/companies/" + formState.id, formData)
                .then((response) => {
                    info("success", response.data.message);
                    visible.value = false;
                    formRef.value.resetFields();
                    emit("companyCreated");
                    emit("handleModalClose");
                })
                .catch((error) => {
                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.errors
                    ) {
                        const errors = error.response.data.errors;
                        const firstError = Object.values(errors)[0][0];
                        info("error", firstError);
                    } else {
                        info('error',"Something went wring");
                    }
                });
        })
        .catch((info) => {
            console.log("Validation Failed:", info);
        });
};
const closeModal = () => {
    visible.value = false;
    emit("handleModalClose", false);
};
</script>

<template>
    <Head title="Dashboard" />

    <div>
        <a-modal
            v-model:open="visible"
            title="Edit comapny"
            ok-text="Update"
            cancel-text="Cancel"
            @ok="onOk"
            @cancel="closeModal"
        >
            <a-form
                ref="formRef"
                :model="formState"
                layout="vertical"
                name="form_in_modal"
            >
                <a-form-item
                    name="name"
                    label="Name"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.name" />
                </a-form-item>

                <a-form-item
                    name="email"
                    label="Email"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.email" />
                </a-form-item>

                <a-form-item name="upload" label="Select logo">
                    <a-upload
                        :file-list="fileList"
                        :before-upload="beforeUpload"
                        @remove="handleRemove"
                        :max-count="1"
                    >
                        <a-button> Select File </a-button>
                    </a-upload>
                </a-form-item>

                <a-form-item
                    name="website"
                    label="Website"
                    :rules="[
                        {
                            required: true,
                            message: 'This field is required.',
                        },
                    ]"
                >
                    <a-input v-model:value="formState.website" />
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>
<style scoped>
.collection-create-form_last-form-item {
    margin-bottom: 0;
}
</style>
