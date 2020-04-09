<template>

	<div class="reply_wrap">

		<div :id="'reply-'+id" class="card-header d-flex">
			<div class="align-self-start">
				<a :href="'/profiles/'+data.owner.name" class="pl-5" v-text="data.owner.name"></a>
				said <span v-text="ago"></span>:
			</div>
			<div class="align-self-end ml-auto" v-if="signedIn">
				<favorite :reply="data"></favorite>
			</div>
		</div>
		<div class="card py-4">
			<div class="panel-body px-5">
				<div v-if="editing">
                    <form @submit.prevent="update">
                        <div class="form-group">
                            <textarea class="form-control" name="body" v-model="body" required></textarea>
                        </div>
                        <button class="btn btn-xs btn-primary">Update</button>
                        <button class="btn btn-xs btn-link" @click="cancel">Cancel</button>
                    </form>
				</div>
				<div v-else v-html="body"></div>
			</div>
			<div class="panel-footer d-flex justify-content-end mt-2 border-secondary border-top pt-4 pr-4" v-if="canUpdate">
				<button class="btn btn-xs btn-dark mr-1" @click="editing = true">Edit</button>
				<button type="submit" class="btn btn-danger btn-xs" @click="destroy">Delete</button>
			</div>
		</div>
	</div>

</template>

<script>

	import Favorite from './Favorite.vue';
	import moment from 'moment';

	export default {

		props: ['data'],

		components: { Favorite },

		data() {
			return {
				editing: false,
				id: this.data.id,
				body: this.data.body,
			}
		},

		computed: {

			ago() {
				return moment(this.data.created_at).fromNow();
			},

			signedIn() {
				return window.App.signedIn;
			},
			canUpdate() {
				return this.authorize(user => this.data.user_id === user.id);

				//return this.data.user_id == window.App.user.id;
			}
		},

		methods: {

			update() {
				axios.patch('/replies/' + this.data.id, {
					body: this.body
				})
				.catch(error => {
					flash(error.response.data, 'danger');
					this.editing = true;
				})
				.then(({data}) => {
					this.editing = false;
					flash('Your reply has been updated');
				});

			},

			destroy() {
				axios.delete('/replies/' + this.data.id);

				this.$emit('deleted', this.data.id);
			},

			cancel() {
				this.editing = false;
				this.body = this.data.body
			}
		}
	};
</script>
