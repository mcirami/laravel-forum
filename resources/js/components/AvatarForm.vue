<template>

    <div>
        <h1 v-text="user.name"></h1>

        <form v-if="canUpdate" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12">
                    <input type="file" name="avatar" accept="image/*" @change="onChange">
                </div>
            </div>

        </form>

        <img :src="avatar" alt="" width="200" height="auto">

    </div>
</template>

<script>
	export default {

		props: ['user'],

        data() {
			return {
				avatar: this.user.avatar_path
            };
        },

		computed: {

			canUpdate() {
				return this.authorize(user => user.id === this.user.id)
            }
        },

        methods: {

			onChange(e) {

				if (! e.target.files.length) return;

				let avatar = e.target.files[0];

				let reader = new FileReader();

				reader.readAsDataURL(avatar);

				reader.onload = e => {

				    this.avatar = e.target.result;
                };

				// Persist to server
                this.persist(avatar);
            },

            persist(avatar) {
                let data = new FormData();

                data.append('avatar', avatar);

                console.log(data);

                axios.post('/api/users/$(this.user.name)/avatar', data)
                    .then(() => flash('Avatar uploaded!'));
            }
        }
	};
</script>

<style scoped>

</style>
