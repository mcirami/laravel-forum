<template>
	<div>

		<div v-if="signedIn">
			<div class="form-group">
				<textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5"
				          v-model="body" required>

				</textarea>
			</div>

			<button type="submit" class="btn btn-primary" @click="addReply">Post</button>
		</div>

		<p class="text-center" v-else>
			Please <a href="/login">sign in</a> to participate in this dicussion
		</p>
	</div>

</template>

<script>

    import 'at.js';
    import 'jquery.caret';

	export default {

		props: ['endpoint'],

		data() {
			return {
				body: "",
			}
		},

		computed: {
			signedIn() {
				return window.App.signedIn;
			}
		},

        mounted() {
		    $('#body').atwho({
                at: "@",
                delay: 750,
                callbacks: {
              	    remoteFilter: function(query, callback) {
              	    	$.getJSON("/api/users", {name:query}, function(usernames) {
              	    		callback(usernames)
                        });
                    }
                }
            })
        },

		methods: {

			addReply() {
				axios.post(this.endpoint, {body: this.body})

				.catch(error => {
					flash(error.response.data, 'danger');
				})

				.then(({data}) => {
					this.body = '';

					flash ('Your reply has been posted');

					this.$emit('created', data);
				});
			}
		}
	};
</script>
