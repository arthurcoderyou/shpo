<div>
    <!-- Subscriber section --> 
        <x-ui.user.subscriber-section
            :users="$users"
            :selectedUsers="$selectedUsers"
            query="query"
            removeAction="removeSubscriber"
        />
    <!-- ./ Subscriber section --> 
    <div class="mt-1.5 text-right">
        <x-ui.button
            type="button"
            sr="Submit Subscribers"
            label="Save"
            x-on:click="
                if (confirm('Are you sure you want to save this subscribers list?')) {
                    $wire.save();
                    openSubscribers = false;
                }
            "
            />
    </div>
     


</div>
