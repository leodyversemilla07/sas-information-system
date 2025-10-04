<?php

namespace App\Enums;

/**
 * Permission Enum
 *
 * Defines all permissions in the system for type-safety and IDE autocomplete.
 * Organized by module: SAS, Registrar, USG, and System-wide.
 */
enum Permission: string
{
    // ========================================
    // SAS Module Permissions
    // ========================================

    // Scholarship Permissions
    case SubmitScholarshipApplication = 'submit_scholarship_application';
    case ViewOwnScholarships = 'view_own_scholarships';
    case ViewAllScholarships = 'view_all_scholarships';
    case ReviewScholarships = 'review_scholarships';
    case ApproveScholarshipsUnder20k = 'approve_scholarships_under_20k';
    case ApproveScholarshipsOver20k = 'approve_scholarships_over_20k';
    case RejectScholarships = 'reject_scholarships';
    case DisbursScholarships = 'disburse_scholarships';

    // Organization Permissions
    case ViewOrganizations = 'view_organizations';
    case ManageOrganizations = 'manage_organizations';
    case CreateOrganizations = 'create_organizations';
    case UpdateOrganizations = 'update_organizations';
    case DeleteOrganizations = 'delete_organizations';
    case ApproveOrganizations = 'approve_organizations';

    // Event Permissions
    case ViewEvents = 'view_events';
    case CreateEvents = 'create_events';
    case ManageEvents = 'manage_events';
    case UpdateEvents = 'update_events';
    case DeleteEvents = 'delete_events';
    case PublishEvents = 'publish_events';

    // Insurance Permissions
    case ViewOwnInsuranceRecords = 'view_own_insurance_records';
    case ViewAllInsuranceRecords = 'view_all_insurance_records';
    case SubmitInsuranceRecords = 'submit_insurance_records';
    case ManageInsuranceRecords = 'manage_insurance_records';
    case ApproveInsuranceRecords = 'approve_insurance_records';

    // SAS Module Access
    case AccessSasModule = 'access_sas_module';
    case ViewSasReports = 'view_sas_reports';

    // ========================================
    // Registrar Module Permissions
    // ========================================

    // Document Request Permissions
    case RequestDocuments = 'request_documents';
    case ViewOwnDocumentRequests = 'view_own_document_requests';
    case ViewAllDocumentRequests = 'view_all_document_requests';
    case ProcessDocumentRequests = 'process_document_requests';
    case ApproveDocumentRequests = 'approve_document_requests';
    case RejectDocumentRequests = 'reject_document_requests';
    case GenerateDocuments = 'generate_documents';

    // Payment Permissions
    case MakePayments = 'make_payments';
    case ViewOwnPayments = 'view_own_payments';
    case ViewAllPayments = 'view_all_payments';
    case ProcessPayments = 'process_payments';
    case IssueRefunds = 'issue_refunds';
    case ManualReconciliation = 'manual_reconciliation';

    // Registrar Module Access
    case AccessRegistrarModule = 'access_registrar_module';
    case ViewRegistrarReports = 'view_registrar_reports';

    // ========================================
    // USG Module Permissions
    // ========================================

    // VMGO Permissions
    case ViewVmgo = 'view_vmgo';
    case ManageVmgo = 'manage_vmgo';
    case UpdateVmgo = 'update_vmgo';

    // Officer Permissions
    case ViewOfficers = 'view_officers';
    case ManageOfficers = 'manage_officers';
    case CreateOfficers = 'create_officers';
    case UpdateOfficers = 'update_officers';
    case DeleteOfficers = 'delete_officers';

    // Announcement Permissions
    case ViewAnnouncements = 'view_announcements';
    case ManageAnnouncements = 'manage_announcements';
    case CreateAnnouncements = 'create_announcements';
    case UpdateAnnouncements = 'update_announcements';
    case DeleteAnnouncements = 'delete_announcements';
    case PublishAnnouncements = 'publish_announcements';

    // Resolution Permissions
    case ViewResolutions = 'view_resolutions';
    case ManageResolutions = 'manage_resolutions';
    case CreateResolutions = 'create_resolutions';
    case UpdateResolutions = 'update_resolutions';
    case DeleteResolutions = 'delete_resolutions';
    case PublishResolutions = 'publish_resolutions';

    // USG Calendar Permissions
    case ViewUsgCalendar = 'view_usg_calendar';
    case ManageUsgCalendar = 'manage_usg_calendar';

    // USG Module Access
    case AccessUsgModule = 'access_usg_module';
    case AccessUsgAdmin = 'access_usg_admin';

    // ========================================
    // System-wide Permissions
    // ========================================

    case ViewOwnRecords = 'view_own_records';
    case ManageUsers = 'manage_users';
    case CreateUsers = 'create_users';
    case UpdateUsers = 'update_users';
    case DeleteUsers = 'delete_users';
    case AssignRoles = 'assign_roles';
    case ManageRoles = 'manage_roles';
    case ManagePermissions = 'manage_permissions';
    case ViewAllModules = 'view_all_modules';
    case SystemConfiguration = 'system_configuration';
    case ViewSystemLogs = 'view_system_logs';
    case ViewAuditLogs = 'view_audit_logs';
    case AccessAdminPanel = 'access_admin_panel';

    /**
     * Get the permission label for display
     */
    public function label(): string
    {
        return str_replace('_', ' ', $this->value);
    }

    /**
     * Get all SAS module permissions
     *
     * @return array<Permission>
     */
    public static function sasPermissions(): array
    {
        return [
            self::SubmitScholarshipApplication,
            self::ViewOwnScholarships,
            self::ViewAllScholarships,
            self::ReviewScholarships,
            self::ApproveScholarshipsUnder20k,
            self::ApproveScholarshipsOver20k,
            self::RejectScholarships,
            self::DisbursScholarships,
            self::ViewOrganizations,
            self::ManageOrganizations,
            self::CreateOrganizations,
            self::UpdateOrganizations,
            self::DeleteOrganizations,
            self::ApproveOrganizations,
            self::ViewEvents,
            self::CreateEvents,
            self::ManageEvents,
            self::UpdateEvents,
            self::DeleteEvents,
            self::PublishEvents,
            self::ViewOwnInsuranceRecords,
            self::ViewAllInsuranceRecords,
            self::SubmitInsuranceRecords,
            self::ManageInsuranceRecords,
            self::ApproveInsuranceRecords,
            self::AccessSasModule,
            self::ViewSasReports,
        ];
    }

    /**
     * Get all Registrar module permissions
     *
     * @return array<Permission>
     */
    public static function registrarPermissions(): array
    {
        return [
            self::RequestDocuments,
            self::ViewOwnDocumentRequests,
            self::ViewAllDocumentRequests,
            self::ProcessDocumentRequests,
            self::ApproveDocumentRequests,
            self::RejectDocumentRequests,
            self::GenerateDocuments,
            self::MakePayments,
            self::ViewOwnPayments,
            self::ViewAllPayments,
            self::ProcessPayments,
            self::IssueRefunds,
            self::ManualReconciliation,
            self::AccessRegistrarModule,
            self::ViewRegistrarReports,
        ];
    }

    /**
     * Get all USG module permissions
     *
     * @return array<Permission>
     */
    public static function usgPermissions(): array
    {
        return [
            self::ViewVmgo,
            self::ManageVmgo,
            self::UpdateVmgo,
            self::ViewOfficers,
            self::ManageOfficers,
            self::CreateOfficers,
            self::UpdateOfficers,
            self::DeleteOfficers,
            self::ViewAnnouncements,
            self::ManageAnnouncements,
            self::CreateAnnouncements,
            self::UpdateAnnouncements,
            self::DeleteAnnouncements,
            self::PublishAnnouncements,
            self::ViewResolutions,
            self::ManageResolutions,
            self::CreateResolutions,
            self::UpdateResolutions,
            self::DeleteResolutions,
            self::PublishResolutions,
            self::ViewUsgCalendar,
            self::ManageUsgCalendar,
            self::AccessUsgModule,
            self::AccessUsgAdmin,
        ];
    }

    /**
     * Get all system-wide permissions
     *
     * @return array<Permission>
     */
    public static function systemPermissions(): array
    {
        return [
            self::ViewOwnRecords,
            self::ManageUsers,
            self::CreateUsers,
            self::UpdateUsers,
            self::DeleteUsers,
            self::AssignRoles,
            self::ManageRoles,
            self::ManagePermissions,
            self::ViewAllModules,
            self::SystemConfiguration,
            self::ViewSystemLogs,
            self::ViewAuditLogs,
            self::AccessAdminPanel,
        ];
    }
}
