@extends('emails.layouts.base')

@section('content')
    <h2>🌿 {{ ucfirst($season) }} Season Health Tips</h2>
    
    @if($patient)
        <p>Dear {{ $patient->patient_name }},</p>
    @else
        <p>Dear Valued Patient,</p>
    @endif
    
    <div class="success-box">
        <p><strong>Stay healthy this {{ $season }} season!</strong> We've prepared some helpful health tips to keep you in the best possible health.</p>
    </div>
    
    @if($season === 'rainy')
        <div class="info-box">
            <h3>🌧️ Rainy Season Wellness</h3>
            <p>The Philippines is currently in the rainy season (June - November). Here's how to stay healthy during this time:</p>
        </div>
    @else
        <div class="info-box">
            <h3>☀️ Dry Season Wellness</h3>
            <p>The Philippines is currently in the dry season (December - May). Here's how to stay healthy during this time:</p>
        </div>
    @endif
    
    @if(count($healthTips) > 0)
        <h3>💡 Health Tips for {{ ucfirst($season) }} Season</h3>
        
        @foreach($healthTips as $index => $tip)
        <div class="info-box">
            <h3>{{ $index + 1 }}. {{ $tip['title'] ?? 'Health Tip' }}</h3>
            <p>{{ $tip['description'] }}</p>
            @if(isset($tip['details']) && is_array($tip['details']))
            <ul style="margin: 10px 0; padding-left: 20px;">
                @foreach($tip['details'] as $detail)
                <li>{{ $detail }}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endforeach
    @else
        <!-- Default seasonal tips if none provided -->
        @if($season === 'rainy')
            <div class="info-box">
                <h3>🦠 Prevent Waterborne Diseases</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Always drink clean, boiled water</li>
                    <li>Avoid street food and ice from unknown sources</li>
                    <li>Wash hands frequently with soap</li>
                    <li>Keep your surroundings clean and free from stagnant water</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>🌡️ Stay Dry and Warm</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Carry an umbrella and wear waterproof clothing</li>
                    <li>Change out of wet clothes immediately</li>
                    <li>Keep feet dry to prevent fungal infections</li>
                    <li>Use dehumidifiers to reduce indoor moisture</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>🦟 Mosquito Protection</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Use mosquito nets while sleeping</li>
                    <li>Apply insect repellent regularly</li>
                    <li>Remove standing water around your home</li>
                    <li>Wear long sleeves and pants during peak mosquito hours</li>
                </ul>
            </div>
        @else
            <div class="info-box">
                <h3>💧 Stay Hydrated</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Drink at least 8-10 glasses of water daily</li>
                    <li>Avoid excessive caffeine and alcohol</li>
                    <li>Eat water-rich fruits like watermelon, oranges, and cucumbers</li>
                    <li>Monitor urine color - it should be light yellow</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>☀️ Sun Protection</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Apply sunscreen with at least SPF 30</li>
                    <li>Wear protective clothing and wide-brimmed hats</li>
                    <li>Avoid direct sun exposure between 10 AM - 4 PM</li>
                    <li>Seek shade whenever possible</li>
                </ul>
            </div>
            
            <div class="info-box">
                <h3>🌡️ Heat-Related Illness Prevention</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Stay in air-conditioned spaces during hot hours</li>
                    <li>Take cool showers or baths</li>
                    <li>Wear light-colored, loose-fitting clothing</li>
                    <li>Limit outdoor activities during peak heat</li>
                </ul>
            </div>
        @endif
    @endif
    
    <div class="success-box">
        <h3>🍎 General Health Reminders</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Maintain a balanced diet</strong> with plenty of fruits and vegetables</li>
            <li><strong>Exercise regularly</strong> - at least 30 minutes of moderate activity daily</li>
            <li><strong>Get adequate sleep</strong> - 7-9 hours per night</li>
            <li><strong>Manage stress</strong> through relaxation techniques and hobbies</li>
            <li><strong>Keep up with preventive care</strong> - regular check-ups and vaccinations</li>
            <li><strong>Practice good hygiene</strong> - hand washing and personal cleanliness</li>
        </ul>
    </div>
    
    @if(isset($additionalData['vaccination_reminders']))
    <div class="alert-box">
        <h3>💉 Vaccination Reminders</h3>
        <p>Don't forget about these important vaccinations for {{ $season }} season:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            @foreach($additionalData['vaccination_reminders'] as $vaccine)
            <li>{{ $vaccine }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div class="info-box">
        <h3>⚠️ When to Seek Medical Care</h3>
        <p>Contact us or seek immediate medical attention if you experience:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            @if($season === 'rainy')
            <li>Persistent fever, especially with headache or body aches</li>
            <li>Severe diarrhea or vomiting</li>
            <li>Difficulty breathing or persistent cough</li>
            <li>Skin rashes or unusual infections</li>
            @else
            <li>Signs of heat exhaustion (nausea, dizziness, weakness)</li>
            <li>Severe dehydration (dark urine, dry mouth, fatigue)</li>
            <li>Heat stroke symptoms (high fever, confusion, hot/dry skin)</li>
            <li>Severe sunburn or skin changes</li>
            @endif
            <li>Any symptoms that concern you or persist</li>
        </ul>
    </div>
    
    @if($patient)
    <div class="info-box">
        <h3>📞 Your Healthcare Team</h3>
        <p>Remember, we're here to support your health journey:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Schedule regular check-ups to monitor your health</li>
            <li>Don't hesitate to contact us with health concerns</li>
            <li>Keep your medications updated and take them as prescribed</li>
            <li>Use your patient portal to access health information</li>
        </ul>
    </div>
    @endif
    
    <p>Your health and well-being are our priority. By following these seasonal health tips, you can enjoy the {{ $season }} season while staying healthy and safe.</p>
    
    <div style="margin-top: 30px;">
        <p><strong>Stay healthy and safe,</strong><br>
        BOKOD CMS Medical Team</p>
    </div>
@endsection