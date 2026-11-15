<?php

namespace Freshdesk\Tests;

use Freshdesk\Api;

/**
 * Asserts that every resource method hits the HTTP method/path documented at
 * https://developer.freshdesk.com/api/
 */
class ResourceRoutesTest extends TestCase
{
    /**
     * @dataProvider routes
     */
    public function test_route(callable $call, string $method, string $path, ?array $query = null, ?array $body = null): void
    {
        $api = $this->api();

        $call($api);

        $request = $this->assertRequest($method, $path, $query);

        if ($body !== null) {
            $this->assertSame($body, $this->jsonBody($request));
        }
    }

    public function routes(): array
    {
        $data = ['name' => 'x'];

        return [
            // Agents
            'agents.all'      => [function (Api $a) { $a->agents->all(); }, 'GET', '/agents'],
            'agents.view'     => [function (Api $a) { $a->agents->view(1); }, 'GET', '/agents/1'],
            'agents.current'  => [function (Api $a) { $a->agents->current(); }, 'GET', '/agents/me'],
            'agents.create'   => [function (Api $a) use ($data) { $a->agents->create($data); }, 'POST', '/agents', null, $data],

            // Companies
            'companies.fields' => [function (Api $a) { $a->companies->fields(); }, 'GET', '/company_fields'],
            'companies.update' => [function (Api $a) use ($data) { $a->companies->update(3, $data); }, 'PUT', '/companies/3', null, $data],

            // Contacts
            'contacts.fields'     => [function (Api $a) { $a->contacts->fields(); }, 'GET', '/contact_fields'],
            'contacts.makeAgent'  => [function (Api $a) { $a->contacts->makeAgent(7, ['occasional' => true]); }, 'PUT', '/contacts/7/make_agent', null, ['occasional' => true]],
            'contacts.hardDelete' => [function (Api $a) { $a->contacts->hardDelete(7); }, 'DELETE', '/contacts/7/hard_delete'],

            // Groups
            'groups.delete' => [function (Api $a) { $a->groups->delete(4); }, 'DELETE', '/groups/4'],

            // Tickets
            'tickets.create'       => [function (Api $a) use ($data) { $a->tickets->create($data); }, 'POST', '/tickets', null, $data],
            'tickets.restore'      => [function (Api $a) { $a->tickets->restore(9); }, 'PUT', '/tickets/9/restore'],
            'tickets.fields'       => [function (Api $a) { $a->tickets->fields(); }, 'GET', '/ticket_fields'],
            'tickets.conversations'=> [function (Api $a) { $a->tickets->conversations(9); }, 'GET', '/tickets/9/conversations'],
            'tickets.timeEntries'  => [function (Api $a) { $a->tickets->timeEntries(9); }, 'GET', '/tickets/9/time_entries'],
            'tickets.search'       => [function (Api $a) { $a->tickets->search('status:2'); }, 'GET', '/search/tickets', ['query' => '"status:2"']],

            // Time entries
            'timeEntries.create' => [function (Api $a) use ($data) { $a->timeEntries->create(9, $data); }, 'POST', '/tickets/9/time_entries', null, $data],
            'timeEntries.all'    => [function (Api $a) { $a->timeEntries->all(9); }, 'GET', '/tickets/9/time_entries'],
            'timeEntries.toggle' => [function (Api $a) { $a->timeEntries->toggle(5); }, 'PUT', '/time_entries/5/toggle_timer'],

            // Conversations
            'conversations.reply'  => [function (Api $a) use ($data) { $a->conversations->reply(9, $data); }, 'POST', '/tickets/9/reply', null, $data],
            'conversations.note'   => [function (Api $a) use ($data) { $a->conversations->note(9, $data); }, 'POST', '/tickets/9/notes', null, $data],
            'conversations.update' => [function (Api $a) use ($data) { $a->conversations->update(2, $data); }, 'PUT', '/conversations/2', null, $data],

            // Discussions
            'discussions.all'      => [function (Api $a) { $a->discussions->all(); }, 'GET', '/discussions/categories'],
            'forums.create'        => [function (Api $a) use ($data) { $a->forums->create(1, $data); }, 'POST', '/discussions/categories/1/forums', null, $data],
            'forums.view'          => [function (Api $a) { $a->forums->view(2); }, 'GET', '/discussions/forums/2'],
            'forums.monitor'       => [function (Api $a) { $a->forums->monitor(2, 8); }, 'POST', '/discussions/forums/2/follow', null, ['user_id' => 8]],
            'forums.unmonitor'     => [function (Api $a) { $a->forums->unmonitor(2, 8); }, 'DELETE', '/discussions/forums/2/follow', ['user_id' => '8']],
            'forums.monitorStatus' => [function (Api $a) { $a->forums->monitorStatus(2, 8); }, 'GET', '/discussions/forums/2/follow', ['user_id' => '8']],
            'topics.create'        => [function (Api $a) use ($data) { $a->topics->create(2, $data); }, 'POST', '/discussions/forums/2/topics', null, $data],
            'topics.all'           => [function (Api $a) { $a->topics->all(2); }, 'GET', '/discussions/forums/2/topics'],
            'topics.unmonitor'     => [function (Api $a) { $a->topics->unmonitor(3, 8); }, 'DELETE', '/discussions/topics/3/follow', ['user_id' => '8']],
            'comments.create'      => [function (Api $a) use ($data) { $a->comments->create(3, $data); }, 'POST', '/discussions/topics/3/comments', null, $data],
            'comments.all'         => [function (Api $a) { $a->comments->all(3); }, 'GET', '/discussions/topics/3/comments'],
            'comments.delete'      => [function (Api $a) { $a->comments->delete(4); }, 'DELETE', '/discussions/comments/4'],

            // Solutions
            'solutions.create'    => [function (Api $a) use ($data) { $a->solutions->create($data); }, 'POST', '/solutions/categories', null, $data],
            'folders.create'      => [function (Api $a) use ($data) { $a->folders->create(1, $data); }, 'POST', '/solutions/categories/1/folders', null, $data],
            'folders.forCategory' => [function (Api $a) { $a->folders->forCategory(1); }, 'GET', '/solutions/categories/1/folders'],
            'folders.view'        => [function (Api $a) { $a->folders->view(2); }, 'GET', '/solutions/folders/2'],
            'articles.create'     => [function (Api $a) use ($data) { $a->articles->create(2, $data); }, 'POST', '/solutions/folders/2/articles', null, $data],
            'articles.forFolder'  => [function (Api $a) { $a->articles->forFolder(2); }, 'GET', '/solutions/folders/2/articles'],
            'articles.view'       => [function (Api $a) { $a->articles->view(3); }, 'GET', '/solutions/articles/3'],

            // Admin
            'products.all'      => [function (Api $a) { $a->products->all(); }, 'GET', '/products'],
            'emailConfigs.all'  => [function (Api $a) { $a->emailConfigs->all(); }, 'GET', '/email_configs'],
            'slaPolicies.all'   => [function (Api $a) { $a->slaPolicies->all(); }, 'GET', '/sla_policies'],
            'businessHours.all' => [function (Api $a) { $a->businessHours->all(); }, 'GET', '/business_hours'],
        ];
    }

    public function test_create_with_attachment_sends_multipart(): void
    {
        $api = $this->api();

        $api->tickets->createWithAttachment([
            ['name' => 'subject', 'contents' => 'Hello'],
            ['name' => 'attachments[]', 'contents' => 'file-bytes', 'filename' => 'a.txt'],
        ]);

        $request = $this->assertRequest('POST', '/tickets');
        $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('filename="a.txt"', (string) $request->getBody());
    }

    public function test_reply_with_attachment_sends_multipart(): void
    {
        $api = $this->api();

        $api->conversations->replyWithAttachment(9, [['name' => 'body', 'contents' => 'Hi']]);

        $request = $this->assertRequest('POST', '/tickets/9/reply');
        $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));
    }
}
