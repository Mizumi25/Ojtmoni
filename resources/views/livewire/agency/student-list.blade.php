<div x-data="{ open: false, signaturePad: new SignaturePad($refs.signatureCanvas) }" class="relative w-full">

    <div class="p-6 bg-white rounded-lg">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-emerald-700">OJT Student List</h2>
            <a href="#" class="text-sm text-emerald-500 hover:underline">See all</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            </div>

        <div class="text-gray-600 text-sm mb-4">
            <span class="text-emerald-700 font-semibold">StudentList</span> > <span>Student Name</span>
        </div>

        <div class="divide-y divide-gray-300">
            @forelse($students as $student)
                <div class="flex items-center py-4">
                    <div class="w-10 h-10 rounded-full overflow-hidden">
                        @if ($student->profile_picture)
                            <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-emerald-200 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-emerald-700 text-lg"></i>
                            </div>
                        @endif
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="font-semibold text-gray-700">{{ $student->name }}</p>
                        <p class="text-sm text-gray-500">{{ $student->email }} | ID: {{ $student->id }} | {{ $student->course->abbreviation ?? 'N/A' }} - {{ $student->yearLevel->name ?? 'Year Unknown' }}</p>
                        <p class="text-sm text-gray-500 truncate">Ready for evaluation. OJT hours completed.</p>
                    </div>
                    <button @click="open = true; $wire.set('selectedStudentId', {{ $student->id }})" class="text-emerald-600 text-sm font-semibold hover:underline ml-auto">Evaluate</button>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No students are ready for evaluation yet.</p>
            @endforelse
        </div>
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 w-full max-w-5xl h-full shadow-lg z-50 overflow-hidden flex">

        <div class="w-1/2 bg-emerald-600 text-white p-6 overflow-y-auto rounded-tl-[3rem] h-[80vh] my-auto">
            <h2 class="text-2xl font-bold mb-4">Evaluation Guide</h2>
            <ul class="list-disc pl-5 space-y-2 text-sm">
                <li>1 - Poor: Rarely meets expectations</li>
                <li>2 - Fair: Occasionally meets expectations</li>
                <li>3 - Good: Consistently meets expectations</li>
                <li>4 - Very Good: Often exceeds expectations</li>
                <li>5 - Excellent: Always exceeds expectations</li>
            </ul>
            <div class="mt-6">
                <h3 class="font-semibold text-lg">Reminder</h3>
                <p class="text-sm mt-2">
                    Please ensure your ratings are honest and constructive. Use the comment section for specific feedback or suggestions.
                </p>
            </div>
        </div>

        <div class="w-1/2 bg-white p-6 overflow-y-auto h-full relative">
            <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <h2 class="text-lg font-bold text-emerald-700 mb-4">Student Evaluation</h2>

            <div class="space-y-6 text-sm text-gray-700">

                <div>
                    <label class="block font-semibold mb-1">Student Name</label>
                    <input type="text" value="{{ $selectedStudent->name ?? '' }}"
                           class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500" readonly />
                </div>

                <div>
                    <label class="block font-semibold mb-2">Performance Criteria</label>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border border-gray-300">
                            <thead class="bg-emerald-100 text-emerald-700">
                            <tr>
                                <th class="px-3 py-2">Criteria</th>
                                @for ($i = 1; $i <= 5; $i++)
                                    <th class="px-2 text-center">{{ $i }}<br><span class="text-xs">
                                        @php $labels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent']; @endphp
                                        {{ $labels[$i - 1] }}
                                    </span></th>
                                @endfor
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $criteria = [
                                    'demonstrates_professionalism',
                                    'communicates_effectively',
                                    'shows_initiative_and_creativity',
                                    'works_well_with_others',
                                    'completes_tasks_on_time',
                                    'follows_company_policies',
                                    'adapts_to_work_environment',
                                ];
                            @endphp
                            @foreach($criteria as $item)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ Str::headline(Str::replace('_', ' ', $item)) }}</td>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <td class="text-center">
                                            <input type="radio"
                                                   wire:model="{{ $item }}_score"
                                                   value="{{ $i }}"
                                                   class="form-radio text-emerald-600"
                                                   name="{{ $item }}"
                                            />
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Technical Skills (1-5)</label>
                    <table class="w-full text-left text-sm">
                        <tr>
                            @for ($i = 1; $i <= 5; $i++)
                                <td class="text-center px-2">
                                    <label for="technical_skills_{{$i}}" class="block text-xs">{{$i}}</label>
                                    <input type="radio"
                                           id="technical_skills_{{$i}}"
                                           wire:model="technical_skills_score"
                                           value="{{ $i }}"
                                           class="form-radio text-emerald-600"
                                           name="technical_skills"
                                    />
                                </td>
                            @endfor
                        </tr>
                    </table>
                    @error('technical_skills_score') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-semibold mb-1">Attendance (1-5)</label>
                    <table class="w-full text-left text-sm">
                        <tr>
                            @for ($i = 1; $i <= 5; $i++)
                                <td class="text-center px-2">
                                    <label for="attendance_{{$i}}" class="block text-xs">{{$i}}</label>
                                    <input type="radio"
                                           id="attendance_{{$i}}"
                                           wire:model="attendance_score"
                                           value="{{ $i }}"
                                           class="form-radio text-emerald-600"
                                           name="attendance"
                                    />
                                </td>
                            @endfor
                        </tr>
                    </table>
                    @error('attendance_score') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div>
                  <label class="block font-semibold mb-1">Overall Performance</label>
                    <div class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500 py-2 px-3 bg-gray-100">
                        {{ $overall_performance_score !== null ? $overall_performance_score . '%' : 'N/A' }}
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Evaluator Comments</label>
                    <textarea rows="4" wire:model="evaluator_comments"
                              class="w-full border-gray-300 rounded-md focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    @error('evaluator_comments') <span class="error">{{ $message }}</span> @enderror
                </div>

                <div class="mt-6">
                    <label class="block font-semibold mb-2">Signature</label>
                    <div class="border rounded-md overflow-hidden w-full h-48 bg-emerald-100 relative">
                        <canvas x-ref="signatureCanvas" class="w-full h-full"></canvas>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" @click="signaturePad.clear()"
                                class="text-sm text-emerald-600 hover:underline">Clear Signature</button>
                        <input type="hidden" wire:model="signature">
                        @error('signature') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
    
                <div class="flex justify-end pt-4">
                    <button type="button" @click="if (!signaturePad.isEmpty()) { $wire.set('signature', signaturePad.toDataURL()); $wire.submitEvaluation(); } else { alert('Please provide a signature.'); }" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md">Submit Evaluation</button>
                </div>
            </div>
        </div>
    </div>
</div>