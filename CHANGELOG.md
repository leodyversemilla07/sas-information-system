# Changelog

All notable changes to the MinSU Bongabong Information System will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
- Initial project setup with Laravel 12 and React 19
- Three-module architecture: Student Affairs Services (SAS), Registrar, and USG Portal
- Inertia.js v2 for seamless server-side and client-side integration
- Comprehensive project documentation suite
- **Role-Based Access Control (RBAC) System**: Complete implementation using Spatie Laravel Permission v6.21.0
  - 8 user roles: `student`, `sas_staff`, `sas_admin`, `registrar_staff`, `registrar_admin`, `usg_officer`, `usg_admin`, `system_admin`
  - 79+ granular permissions organized by module (SAS, Registrar, USG, System)
  - Type-safe Role and Permission enums (`app/Enums/Role.php`, `app/Enums/Permission.php`)
  - Database seeder (`RoleAndPermissionSeeder`) with complete role-permission assignments
  - 6 authorization policies in modular structure:
    - SAS: `ScholarshipPolicy` (with ₱20k threshold approval logic), `OrganizationPolicy`, `EventPolicy`
    - Registrar: `DocumentRequestPolicy`
    - USG: `AnnouncementPolicy`, `ResolutionPolicy`
  - Middleware aliases registered: `role`, `permission`, `role_or_permission`
  - User factory enhanced with role-specific states for testing
  - Two-factor authentication columns in users table (`two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`)

### Changed
- **Authentication Stack Clarification**: Removed Laravel Sanctum from core architecture
  - Using session-based authentication via Laravel Fortify for Inertia.js SPA
  - Sanctum can be added later if external API access or mobile apps are required
  - Updated ARCHITECTURE.md to reflect Fortify + Spatie Permission stack
  - Updated API_SPECIFICATIONS.md to use session-based authentication endpoints
  - Session management provides sufficient security for single-campus SPA deployment
- **User Model**: Added `HasRoles` trait from Spatie Laravel Permission for RBAC functionality
- **Database Structure**: Consolidated two-factor authentication columns into main users table migration

### Deprecated
- N/A

### Removed
- Laravel Sanctum from core technology stack (can be added later if needed)

### Fixed
- N/A

### Security
- N/A

---

## [1.0.0] - 2025-10-04

_Initial documentation release._

### Added

#### Documentation Standardization
- Unified project name across all documents to "MinSU Bongabong Information System"
- Standardized campus name format to "Mindoro State University - Bongabong Campus"
- Created comprehensive DIRECTORY_STRUCTURE.md as single source of truth for file organization
- Added placeholder domain disclaimers in API_SPECIFICATIONS.md and NFR.md
- Added technology stack overview section in PRD.md with cross-references to ARCHITECTURE.md
- Added explicit database naming conventions section in DATA_MODELS.md

#### Core Documentation Files
- PRD.md - Product Requirements Document
- USER_STORIES.md - Detailed user scenarios and acceptance criteria
- DATA_MODELS.md - Database schema and entity relationships
- API_SPECIFICATIONS.md - RESTful API endpoint contracts
- NFR.md - Non-functional requirements and performance metrics
- ARCHITECTURE.md - Technical architecture and system design
- DIRECTORY_STRUCTURE.md - Complete project file organization reference

#### Project Structure
- Complete Laravel 12 project structure following framework conventions
- Module-specific directory organization (SAS, Registrar, USG)
- Database table prefixing strategy:
  - No prefix: Shared tables (users, roles, permissions)
  - `sas_` prefix: Student Affairs module
  - `registrar_` prefix: Registrar module  
  - `usg_` prefix: USG Portal module
- Testing structure with Feature/ and Unit/ test organization

#### Technology Stack
- Laravel 12 (PHP 8.2+)
- React 19 with TypeScript 5.x
- Inertia.js v2
- Tailwind CSS 4.x
- MySQL 8.0+
- Vite 6.x for asset bundling
- Pest for testing framework

### Changed
- Expanded ARCHITECTURE.md directory structure section to include all subdirectories
- Updated all documentation files to use consistent terminology and naming conventions
- Unified date format to ISO 8601 (YYYY-MM-DD) across all documents
- Version number set to 1.0 across all documentation

### Fixed
- Resolved inconsistent project name usage between "SAS Information System" and "MinSU Bongabong Information System"
- Corrected campus name formatting inconsistencies
- Added missing cross-references between documentation files
- Fixed missing database naming convention documentation

---

## Document Relationships

```
PRD.md (Product Requirements)
├── References → ARCHITECTURE.md (for tech stack details)
├── References → DIRECTORY_STRUCTURE.md (for file organization)
├── Defines Features → USER_STORIES.md (detailed user scenarios)
├── Specifies Data → DATA_MODELS.md (database schema)
├── Requires APIs → API_SPECIFICATIONS.md (endpoint contracts)
└── Sets Standards → NFR.md (non-functional requirements)

ARCHITECTURE.md (Technical Architecture)
├── Implements → PRD.md requirements
├── References → DIRECTORY_STRUCTURE.md (for detailed file structure)
├── Defines → DATA_MODELS.md structure
└── Enables → API_SPECIFICATIONS.md endpoints

DIRECTORY_STRUCTURE.md (File Organization)
├── Referenced by → ARCHITECTURE.md (high-level overview)
├── Referenced by → PRD.md (file locations)
├── Defines → Complete project file tree
├── Specifies → Naming conventions (backend & frontend)
└── Documents → Laravel 12 specific structure
```

---

## Maintenance Guidelines

### When to Update This Changelog

1. **New Feature Addition**:
   - Add entry under `[Unreleased]` → `Added` section
   - Update PRD.md, USER_STORIES.md, API_SPECIFICATIONS.md, and DATA_MODELS.md accordingly

2. **Bug Fixes**:
   - Add entry under `[Unreleased]` → `Fixed` section
   - Reference issue number if applicable

3. **Breaking Changes**:
   - Prefix entry with `**Breaking:**` in bold
   - Add entry under appropriate section (`Changed`, `Removed`, etc.)
   - Update version number following semantic versioning

4. **Security Patches**:
   - Add entry under `[Unreleased]` → `Security` section
   - Consider immediate release for critical vulnerabilities

5. **Release Process**:
   - Move all `[Unreleased]` entries to new version section
   - Add release date in ISO 8601 format (YYYY-MM-DD)
   - Create git tag matching version number
   - Update version references in all documentation

### Version Numbering

This project follows [Semantic Versioning](https://semver.org/):
- **MAJOR** version (X.0.0): Incompatible API changes or breaking changes
- **MINOR** version (0.X.0): New functionality in a backward-compatible manner
- **PATCH** version (0.0.X): Backward-compatible bug fixes

### Review Frequency

- **Weekly**: During active development sprints
- **Per Sprint**: Before sprint review meetings
- **Before Release**: Complete changelog audit for accuracy and consistency

---

## Contact

For questions about this changelog or to report issues:
- **Development Team**: MinSU Bongabong Campus IT
- **Project Repository**: [GitHub/GitLab URL]
- **Issue Tracker**: [Issue Tracker URL]

---

**Last Updated:** October 4, 2025  
**Next Review:** Weekly during active development  
**Maintained By:** Development Team

[Unreleased]: https://github.com/yourusername/sas-information-system/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/yourusername/sas-information-system/releases/tag/v1.0.0
