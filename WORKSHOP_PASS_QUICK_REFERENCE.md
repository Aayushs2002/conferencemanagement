# Workshop Pass Designation - Quick Reference

## What Was Added

Workshop trainer and participant pass designations to the conference pass settings.

## New Pass Hierarchy (Priority Order)

1. 🔵 **User-Specific Designation** (ConferenceUserPassDesignation)
2. 🟢 **Committee Designation** (ConferenceCommitteePassDesignation)
3. 🆕 **Workshop Designation** ← NEW!
   - Workshop Trainer (registrant_type = 2)
   - Workshop Participant (registrant_type = 1)
4. 🟡 **Member Type Designation** (ConferenceMemberTypeNameTag)
5. ⚪ **Fallback Designation** (based on registrant type)

## How to Configure

1. Go to: **Conference Dashboard → Pass Setting → Create/Edit**

2. Find **Section 1.1: Workshop Pass Setting**

3. Fill in the fields:
   - **Workshop Participant Name Tag**: e.g., "Workshop Participant"
   - **Workshop Participant Color**: Choose color
   - **Workshop Trainer Name Tag**: e.g., "Workshop Trainer"  
   - **Workshop Trainer Color**: Choose color

4. Click **Submit** or **Update**

5. ✅ Done! Passes will now show workshop designations when applicable

## When It's Used

✅ User is registered for a workshop (status = 1)  
✅ Workshop designation is configured in pass settings  
✅ User doesn't have higher priority designation (user-specific or committee)  

## Database Changes

**Table**: `pass_settings`

| Column | Type | Description |
|--------|------|-------------|
| `workshop_participant_name_tag` | VARCHAR | Name tag for participants |
| `workshop_participant_color` | VARCHAR | Hex color for participants |
| `workshop_trainer_name_tag` | VARCHAR | Name tag for trainers |
| `workshop_trainer_color` | VARCHAR | Hex color for trainers |

✅ Migration: `2026_02_06_212426_add_workshop_pass_fields_to_pass_settings_table.php`  
✅ Status: **Successfully Executed**

## Example Configurations

### Example 1: Full Workshop Designation
```
Workshop Participant Name Tag: "Workshop Participant"
Workshop Participant Color: #FF6B6B
Workshop Trainer Name Tag: "Workshop Facilitator"
Workshop Trainer Color: #4ECDC4
```

### Example 2: Trainers Only
```
Workshop Participant Name Tag: (leave empty)
Workshop Participant Color: (leave empty)
Workshop Trainer Name Tag: "Resource Person"
Workshop Trainer Color: #FFD93D
```

### Example 3: Not Using Workshop Designation
```
All fields: (leave empty)
→ System will use member type or fallback designation
```

## Files Changed

✅ **Migration**: Added 4 columns to pass_settings  
✅ **Model**: PassSetting.php - Added fillable fields  
✅ **Controller**: PassSettingController.php - Added validation  
✅ **View**: create.blade.php - Added UI fields  
✅ **Pass Generation**: ConferenceRegistrationController.php - Updated hierarchy  

## Testing

**Test as Workshop Trainer**:
1. Register user for workshop with registrant_type = 2
2. Generate pass
3. Should show trainer designation

**Test as Workshop Participant**:
1. Register user for workshop with registrant_type = 1  
2. Generate pass
3. Should show participant designation

**Test Hierarchy**:
1. Give user committee designation
2. Register same user for workshop
3. Generate pass
4. Should show committee designation (higher priority)

## Benefits

✅ Flexible designation for workshop users  
✅ Optional - can leave unused  
✅ Respects existing hierarchy  
✅ No impact on existing functionality  
✅ Easy to configure  
✅ Safe fallbacks  

---

**Status**: ✅ **READY TO USE**

For detailed documentation, see: [WORKSHOP_PASS_DESIGNATION_IMPLEMENTATION.md](WORKSHOP_PASS_DESIGNATION_IMPLEMENTATION.md)
