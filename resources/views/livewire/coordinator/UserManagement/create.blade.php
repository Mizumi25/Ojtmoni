<div class="p-4">
    <h2 class="text-lg font-semibold mb-4">Create User</h2>

    <form>
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Name</label>
            <input type="text" class="w-full p-2 border rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" class="w-full p-2 border rounded-md">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Role</label>
            <select class="w-full p-2 border rounded-md">
                <option>Select Role</option>
                <option>Coordinator</option>
                <option>Student</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Password</label>
            <input type="password" class="w-full p-2 border rounded-md">
        </div>

        <div class="flex justify-end space-x-2">
            <button @click="openCreate = false" type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Create</button>
        </div>
    </form>
</div>
