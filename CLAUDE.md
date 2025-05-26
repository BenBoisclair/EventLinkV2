# EventLink Event Manager - Product Requirements & Technical Documentation

## Product Overview

EventLink Event Manager is a comprehensive SaaS platform designed to empower event organizers throughout the entire event lifecycle - from pre-event planning to post-event analytics. Our initial focus is on exhibition-type events including trade shows, conferences, and workshops.

### Vision
To become the all-in-one platform that event organizers rely on to seamlessly manage every aspect of their events, reducing complexity and increasing attendee engagement.

### Target Users
- Event organizers managing trade shows, conferences, and workshops
- Exhibition coordinators handling multiple exhibitors
- Marketing teams creating event websites and registration forms

## Product Lifecycle Coverage

### Pre-Event Phase
- **Event Planning**: Create and configure event details
- **Website Builder**: Drag-and-drop website creation with customizable blocks
- **Exhibitor Management**: Invite and manage exhibitor profiles
- **Registration System**: Custom attendee registration forms
- **Marketing Tools**: Public event websites with SEO optimization

### During Event Phase (Planned)
- Real-time attendee check-in
- Live event updates
- Exhibitor showcase features
- On-site registration support

### Post-Event Phase (Planned)
- Event analytics and reporting
- Attendee feedback collection
- Lead generation reports for exhibitors
- Post-event communication tools

## Technical Architecture

### Tech Stack

**Backend Framework**
- Laravel 12 (PHP 8.2+) - Modern PHP framework
- Laravel Jetstream - Authentication and team management
- Laravel Sanctum - API authentication
- Laravel Reverb - WebSocket server for real-time features

**Frontend Technologies**
- Vue 3 with Composition API - Reactive UI framework
- TypeScript - Type safety for better developer experience
- Inertia.js - Seamless SPA-like navigation
- Pinia - State management
- Tailwind CSS - Utility-first styling
- Vuetify - Material Design components

**Infrastructure**
- Database: SQLite (dev), MySQL/PostgreSQL (production)
- File Storage: S3-compatible storage with local fallback
- Queue System: Laravel queues for background jobs
- WebSockets: Real-time collaboration features

### Domain Model

```
Organization (Team)
├── Users (Team Members)
├── Events
│   ├── Event Details (name, dates, location, description)
│   ├── Website (one-to-one)
│   │   ├── Settings (theme, meta, favicon)
│   │   └── Blocks (customizable content modules)
│   ├── Attendees (registrations)
│   └── Exhibitors (many-to-many relationship)
```

### Key Features

#### 1. Multi-Tenant Architecture
- Team-based organization management
- Role-based access control
- Team member invitations

#### 2. Event Management
- Create and manage multiple events per organization
- Event dashboard with key statistics
- Event-specific settings and configuration

#### 3. Website Builder
- **Block-Based System**: Modular content blocks
  - Hero Block: Eye-catching header with CTAs
  - Description Block: Rich text content
  - Countdown Block: Event countdown timer
  - Stats Block: Key event statistics
  - Attendees Form Block: Registration forms
  - Exhibitor Showcase Block: Exhibitor gallery
  - Canvas Block: Custom drawing/design tool
- **Real-Time Collaboration**: Multiple users can edit simultaneously
- **Responsive Design**: Desktop, tablet, and mobile previews
- **Publishing Workflow**: Draft and published states
- **Custom Domains**: Support for custom event URLs

#### 4. Attendee Management
- Custom registration forms with flexible fields
- Attendee data storage and management
- Export capabilities (planned)
- Registration analytics

#### 5. Exhibitor Management
- Exhibitor profiles with logos and banners
- Showcase functionality for public display
- Many-to-many relationships with events
- Image upload and processing

## Development Guidelines

### Essential Commands

```bash
# Development
composer dev              # Start all services (Laravel, Vite, Queue, Logs)
php artisan serve        # Laravel development server
pnpm run dev            # Vite development server
php artisan queue:listen # Process background jobs
php artisan pail        # Real-time log viewer

# Building & Deployment
pnpm run build          # Production frontend build
pnpm run ziggy          # Generate JavaScript routes
composer test           # Run test suite

# Database
php artisan migrate     # Run migrations
php artisan migrate:fresh --seed  # Reset with seed data
```

### Project Structure

```
app/
├── Actions/            # Business logic separated from controllers
│   ├── Attendee/      # Attendee-related actions
│   ├── Website/       # Website publishing logic
│   └── Fortify/       # Authentication actions
├── Models/            # Eloquent models
├── Http/Controllers/  # Web and API controllers
├── Jobs/              # Background job processing
└── Policies/          # Authorization rules

resources/js/
├── Components/        # Vue components
│   ├── UI/           # Reusable UI components
│   ├── Forms/        # Form components
│   ├── WebsiteBuilder/  # Website builder blocks
│   └── [Feature]/    # Feature-specific components
├── Pages/            # Inertia page components
├── Composables/      # Reusable Vue logic
├── Stores/           # Pinia state management
├── Services/         # API and utility services
└── Types/            # TypeScript definitions
```

### Code Patterns & Conventions

#### Actions Pattern
Business logic is separated into action classes:
```php
// app/Actions/Attendee/RegisterAttendee.php
class RegisterAttendee
{
    public function execute(Event $event, array $data): Attendee
    {
        // Validation and business logic
    }
}
```

#### Vue Composables
Reusable logic extracted into composables:
```typescript
// resources/js/Composables/useBlockEditor.ts
export function useBlockEditor() {
    // Shared block editing logic
}
```

#### Type Safety
TypeScript interfaces for all data structures:
```typescript
// resources/js/types/event.ts
export interface Event {
    id: number;
    name: string;
    // ...
}
```

#### Real-Time Updates
WebSocket events for collaborative features:
```javascript
// Listen for block updates
Echo.private(`website.${websiteId}`)
    .listen('BlockUpdated', (e) => {
        // Update local state
    });
```

## API Structure

### Public Endpoints
- `GET /site/{slug}` - View published event website
- `POST /events/{event}/attendees/register` - Public registration

### Authenticated Endpoints

#### Events
- `GET /organiser/events` - List user's events
- `POST /organiser/events` - Create new event
- `PATCH /organiser/events/{event}` - Update event

#### Website Builder
- `GET /builder/{slug}` - Website builder interface
- `POST /events/{event}/website/create` - Initialize website
- `POST /events/{event}/website/{website}/save` - Save changes
- `POST /events/{event}/website/{website}/publish` - Publish website

#### Attendees
- `GET /organiser/events/{event}/attendees` - List attendees
- `POST /organiser/events/{event}/attendees` - Manual registration
- `PATCH /organiser/events/{event}/attendees/{attendee}` - Update
- `DELETE /organiser/events/{event}/attendees/{attendee}` - Remove

#### Exhibitors
- Similar CRUD operations for exhibitor management

## Integrations

### Storage (Active)
- **AWS S3**: File storage for images and favicons
- Bucket configured with "Bucket owner enforced" (no ACLs)
- Public access via bucket policy, not individual ACLs
- See `docs/S3_SETUP_GUIDE.md` for configuration details

### Payment Processing (Planned)
- **Stripe**: For subscription billing and event ticketing
- Subscription tiers for different organization sizes
- Usage-based pricing considerations

### Analytics (Planned)
- **PostHog**: Internal product analytics
- Custom event tracking for organizer websites
- Attendee engagement metrics

### Future Integrations
- Email service providers (SendGrid/Postmark)
- Calendar integrations (Google/Outlook)
- Marketing automation tools
- Check-in hardware/apps

## Security & Compliance

### Current Implementation
- Team-based access control
- Sanctum API token authentication
- CSRF protection
- XSS prevention through Vue
- Validated and sanitized inputs

### Planned Enhancements
- Two-factor authentication (already scaffolded)
- GDPR compliance tools
- Data export capabilities
- Audit logging

## Performance Considerations

### Current Optimizations
- Queue-based image processing
- Efficient database queries with eager loading
- Frontend code splitting
- CDN-ready asset pipeline

### Scalability Planning
- Database read replicas
- Redis caching layer
- Horizontal scaling capability
- Multi-region deployment support

## Development Workflow

### Testing
```bash
composer test                          # Run all tests
php artisan test --filter=EventTest    # Run specific test
```

### Code Quality
- Follow Laravel best practices
- Use TypeScript for type safety
- Write tests for critical paths
- Document complex business logic

### Git Workflow
- Feature branches from `main`
- Pull requests with code review
- Automated testing in CI/CD
- Semantic versioning

## Roadmap Vision

### Phase 1: Foundation (Current)
- ✅ Multi-tenant architecture
- ✅ Basic event management
- ✅ Website builder with core blocks
- ✅ Attendee registration
- ✅ Exhibitor management
- 🔄 Publishing workflow

### Phase 2: Enhancement
- [ ] Advanced registration forms
- [ ] Payment integration
- [ ] Email notifications
- [ ] Basic analytics
- [ ] Mobile app (React Native)

### Phase 3: Scale
- [ ] White-label options
- [ ] Advanced analytics
- [ ] Marketing automation
- [ ] API for third-party integrations
- [ ] Multi-language support

### Phase 4: Enterprise
- [ ] SSO support
- [ ] Advanced permissions
- [ ] Custom workflows
- [ ] Enterprise reporting
- [ ] SLA guarantees

## Support & Maintenance

### Monitoring (Planned)
- Application performance monitoring
- Error tracking (Sentry)
- Uptime monitoring
- User session recording

### Customer Support
- In-app documentation
- Video tutorials
- Email support
- Community forum (future)

## Business Metrics

### Key Performance Indicators
- Monthly Active Organizations
- Events Created per Month
- Attendee Registrations
- Website Page Views
- Feature Adoption Rates

### Success Metrics
- User retention rate
- Time to first event creation
- Support ticket volume
- Feature usage analytics

---

## Quick Reference

### Common Tasks

**Create a new block type:**
1. Create component in `resources/js/Components/WebsiteBuilder/Blocks/[BlockName]/`
2. Add `index.vue` (display) and `Editor.vue` (edit interface)
3. Register in `resources/js/utils/blockRegistry.ts`
4. Add to block defaults in `blockDefaults.ts`

**Add a new API endpoint:**
1. Create controller method
2. Add route in `routes/web.php` or `routes/api.php`
3. Create TypeScript types
4. Add API service method

**Implement a new feature:**
1. Create database migration if needed
2. Update models and relationships
3. Create Action class for business logic
4. Build Vue components
5. Add to appropriate pages
6. Write tests

### Debugging

```bash
# View real-time logs
php artisan pail

# Clear all caches
php artisan optimize:clear

# Debug database queries
DB::enableQueryLog();
// ... code ...
dd(DB::getQueryLog());
```

---

*This document is maintained as part of the EventLink Event Manager codebase and should be updated as the product evolves.*